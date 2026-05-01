@php
    // CategoryParams değişkeni yoksa eski yöntemle al (sadece ana parametreler)
    if (!isset($categoryParams)) {
        $categoryParams = $product->customizationPivotParams
            ->where('param.customization_category_id', $category->id)
            ->where('customization_params_ust_id', 0);
    }

    // file/files type kategoriler artık wizard'da render edilmiyor
    // (dosya yüklemesi sipariş seviyesine taşındı, checkout'ta tek ZIP)
    if (in_array($category->type, ['file', 'files'])) {
        return;
    }

    // Hidden kategoriler için görüntülenebilir item kontrolü
    $hasVisibleItems = false;
    if ($category->type == 'hidden') {
        $user = auth()->user();
        if ($user && $user->customer_id) {
            // Bu kategorideki tüm parametreler için kullanıcının erişimi var mı kontrol et
            foreach ($categoryParams as $pivot) {
                $hasAccess = \App\Models\CustomizationParamsCustomersPivot::where([
                    'customer_id' => $user->customer_id,
                    'customization_params_id' => $pivot->param->id,
                    'product_id' => $product->id
                ])->exists();

                if ($hasAccess) {
                    $hasVisibleItems = true;
                    break;
                }
            }
        } 
    } else {
        // Hidden olmayan kategoriler için her zaman görünür
        $hasVisibleItems = true;
    }
@endphp

@if($hasVisibleItems)
<div class="customization-section mb-4" data-category="{{ $category->id }}" data-type="{{ $category->type }}" data-required="{{ $category->required }}">
    <h4 >{{ $category->title }}</h4>
    
    @if($category->id == 11)
        <div id="template-download-link" style="display: none; margin-bottom: 15px;">
            <span class="bg bg-danger p-2" style="margin-bottom: 10px;">Lütfen Kapak Şablonuna uygun şekilde kapak tasarımı yapın ve yükleyin</span><br><br>
            <a href="#" id="template-link" class="btn btn-outline-primary btn-sm" download>
                <i class="bi bi-download"></i> Şablonu İndir
            </a>
        </div>
    @endif

@if($category->params && $category->params->count() > 0)
    {{-- Tüm tek-seçim tipleri (radio, hidden, select) aynı uniform card pattern.
         Cascade chain filter'ı tek mekanizma; eski mirror-select + hidden <select>
         + AJAX child loading kaldırıldı (duplicate ID problemi yaratıyordu). --}}
    @if($category->type == 'radio' || $category->type == 'hidden' || $category->type == 'select')

        <div class="row g-3 option-card-grid">
        @foreach($categoryParams as $pivot)
            @php
                $param = $pivot->param;
                $hasChildren = $product->customizationPivotParams->where('customization_params_ust_id', $pivot->id)->count() > 0;
                $hasImage = $param->option2 == 'true' && !empty($param->value);

                // Hidden tipi için kullanıcı kontrolü
                $showHiddenCategory = true;
                if ($category->type == 'hidden') {
                    $user = auth()->user();
                    if ($user && $user->customer_id) {
                        $hasAccess = \App\Models\CustomizationParamsCustomersPivot::where([
                            'customer_id' => $user->customer_id,
                            'customization_params_id' => $param->id,
                            'product_id' => $product->id
                        ])->exists();
                        $showHiddenCategory = $hasAccess;
                    } else {
                        $showHiddenCategory = false;
                    }
                }
            @endphp

            @if($showHiddenCategory)
            <div class="col-6 col-md-4 col-lg-3 option-card-wrapper" data-parent-pivot-id="{{ (int)$pivot->customization_params_ust_id }}">
                <label class="option-card" for="pivot_{{ $pivot->id }}">
                    <input class="option-card-input customization-radio"
                           type="radio"
                           name="customizations[0][{{ $category->id }}]"
                           value="{{ $pivot->id }}"
                           id="pivot_{{ $pivot->id }}"
                           data-price="{{ $pivot->price ?: 0 }}"
                           data-title="{{ $param->key }}"
                           data-pivot-id="{{ $pivot->id }}"
                           data-has-children="{{ $hasChildren ? 'true' : 'false' }}"
                           data-is-child="{{ $pivot->customization_params_ust_id > 0 ? 'true' : 'false' }}"
                           data-parent-pivot-id="{{ (int)$pivot->customization_params_ust_id }}"
                           data-parent-id="{{ (int)$pivot->customization_params_ust_id }}"
                           data-category-id="{{ $category->id }}"
                           data-category-title="{{ $category->title }}">
                    <div class="option-card-image-wrap">
                        @if($hasImage)
                            <img src="{{ asset('storage/' . $param->value) }}"
                                 alt="{{ $param->key }}"
                                 class="option-card-image"
                                 loading="lazy">
                        @else
                            <div class="option-card-no-image">
                                <i class="fas fa-cube"></i>
                            </div>
                        @endif
                        <span class="option-card-checkmark"><i class="fas fa-check"></i></span>
                    </div>
                    <div class="option-card-body">
                        <div class="option-card-title">{{ $param->key }}</div>
                        @if(($pivot->price ?: 0) > 0)
                            @if(Auth::check() and Auth::user()->roles->contains('id', 3) or Auth::user()->roles->contains('id', 1))
                            <div class="option-card-price">+{{ number_format($pivot->price ?: 0, 2) }} ₺</div>
                            @endif
                        @endif
                    </div>
                </label>
            </div>
            @endif
        @endforeach
        </div>


    @elseif($category->type == 'checkbox')

        <div class="row g-3 option-card-grid">
        @foreach($categoryParams as $pivot)
            @php
                $param = $pivot->param;
                $hasImage = $param->option2 == 'true' && !empty($param->value);
            @endphp
            <div class="col-6 col-md-4 col-lg-3">
                <label class="option-card option-card-multi" for="pivot_checkbox_{{ $pivot->id }}">
                    <input class="option-card-input customization-checkbox"
                           type="checkbox"
                           name="customizations[0][{{ $category->id }}][]"
                           value="{{ $pivot->id }}"
                           id="pivot_checkbox_{{ $pivot->id }}"
                           data-price="{{ $pivot->price ?? 0 }}"
                           data-title="{{ $param->key }}"
                           data-category-id="{{ $category->id }}"
                           data-category-title="{{ $category->title }}">
                    <div class="option-card-image-wrap">
                        @if($hasImage)
                            <img src="{{ asset('storage/' . $param->value) }}"
                                 alt="{{ $param->key }}"
                                 class="option-card-image"
                                 loading="lazy">
                        @else
                            <div class="option-card-no-image">
                                <i class="fas fa-plus-square"></i>
                            </div>
                        @endif
                        <span class="option-card-checkmark"><i class="fas fa-check"></i></span>
                    </div>
                    <div class="option-card-body">
                        <div class="option-card-title">{{ $param->key }}</div>
                        @if(($pivot->price ?: 0) > 0)
                            @if(Auth::check() and Auth::user()->roles->contains('id', 3) or Auth::user()->roles->contains('id', 1))
                            <div class="option-card-price">+{{ number_format($pivot->price ?: 0, 2) }} ₺</div>
                            @endif
                        @endif
                    </div>
                </label>
            </div>
        @endforeach
        </div>


    @elseif($category->type == 'file')


        @foreach($categoryParams as $pivot)
            @php
                $param = $pivot->param;
            @endphp
            <div class="col-md-6">
                <div class="form-group">
                    <input class="form-control customization-file"
                           type="file"
                           name="customizations[0][{{ $category->id }}][file][]"
                           id="param_file_{{ $param->id }}"
                           data-price="{{ $pivot->price ?? 0 }}"
                           data-title="{{ $param->key }}"
                           data-param-id="{{ $param->id }}"
                           data-pivot-id="{{ $pivot->id }}"
                           data-pivot-param-id="{{ $pivot->id }}"
                           data-category-id="{{ $category->id }}"
                           data-category-title="{{ $category->title }}"
                           data-product-id="{{ $product->id }}"
                           data-category-type="{{ $category->type }}"
                           data-input-type="{{ $category->type }}">

                    <!-- Yüklenen dosyaların önizlemesi -->
                    <div id="file_preview_{{ $param->id }}" class="mt-2"></div>

                    <small class="text-muted d-block mt-1">
                        <i class="fas fa-info-circle"></i> Maksimum dosya boyutu: <strong>500 MB</strong> (toplam)
                    </small>

                </div>
            </div>
        @endforeach

    </div>


    @elseif( $category->type == 'files')

    @foreach($categoryParams as $pivot)
        @php
            $param = $pivot->param;
        @endphp
        <div class="col-md-12"> 
            <div class="form-group">
                <label for="param_file_{{ $param->id }}" class="form-label fw-bold">
                    <i class="fas fa-file-archive"></i> {{ $param->key }}
                    @if(($pivot->price ?: 0) > 0)
                        @if(Auth::check() and Auth::user()->roles->contains('id', 3) or Auth::user()->roles->contains('id', 1))
                        <span class="text-success-2">(+{{ number_format($pivot->price ?: 0, 2) }} TL)</span>
                        @endif
                    @endif
                </label>
                
                <input class="form-control customization-zip-file"
                       type="file"
                       name="customizations[0][{{ $category->id }}][zip_file]"
                       id="param_file_{{ $param->id }}"
                       data-price="{{ $pivot->price ?? 0 }}"
                       data-title="{{ $param->key }}"
                       data-param-id="{{ $param->id }}"
                       data-pivot-id="{{ $pivot->id }}"
                       data-pivot-param-id="{{ $pivot->id }}"
                       data-category-id="{{ $category->id }}"
                       data-category-title="{{ $category->title }}"
                       data-product-id="{{ $product->id }}"
                       data-category-type="{{ $category->type }}"
                       data-input-type="zip"
                       accept=".zip,.rar,.7z">

                <!-- Yüklenen dosya bilgisi -->
                <div id="file_preview_{{ $param->id }}" class="mt-2"></div>
                
                <small class="text-muted d-block mt-1">
                    <i class="fas fa-info-circle"></i> Sadece sıkıştırılmış dosya yükleyebilirsiniz (.zip, .rar, .7z) — Maksimum dosya boyutu: <strong>500 MB</strong>
                </small>
            </div>
        </div>
    @endforeach

</div>

    @elseif($category->type == 'input')

        @foreach($categoryParams as $pivot)
            @php
                $param = $pivot->param;
                // Bu parametrenin child'ları var mı kontrol et - customization_params_ust_id pivot ID'si
                $hasChildren = $product->customizationPivotParams->where('customization_params_ust_id', $pivot->id)->count() > 0;
            @endphp

            <div class="col-md-6">
                <div class="form-group">

                    <input type="text"
                           class="form-control customization-input"
                           name="customizations[0][{{ $category->id }}]"
                           id="input_{{ $param->id }}"
                           data-param-id="{{ $param->id }}"
                           data-title="{{ $param->key }}"
                           data-price="{{ $pivot->price ?: 0 }}"
                           data-category-id="{{ $category->id }}"
                           data-category-title="{{ $category->title }}"
                           value=""
                           @if($param->required == 1) required @endif>
                    @if(($pivot->price ?: 0) > 0)
                        @if(Auth::check() and Auth::user()->roles->contains('id', 3) or Auth::user()->roles->contains('id', 1))
                        <small class="text-success-2">(+{{ number_format($pivot->price ?: 0, 2) }} TL)</small>
                        @endif
                        @endif
                </div>
            </div>
        @endforeach



    @endif
@endif
<div class="child-parameters-container"></div>
</div>

@endif
