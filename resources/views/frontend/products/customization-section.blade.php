@php
    // CategoryParams değişkeni yoksa eski yöntemle al (sadece ana parametreler)
    if (!isset($categoryParams)) {
        $categoryParams = $product->customizationPivotParams
            ->where('param.customization_category_id', $category->id)
            ->where('customization_params_ust_id', 0);
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
    @if($category->type == 'radio' || $category->type == 'hidden')

        @foreach($categoryParams as $pivot)
            @php
                $param = $pivot->param;
                // Bu parametrenin child'ları var mı kontrol et - customization_params_ust_id pivot ID'si
                $hasChildren = $product->customizationPivotParams->where('customization_params_ust_id', $pivot->id)->count() > 0;
                // Bu parametrenin resmi var mı kontrol et (option2 = true ise)
                $hasImage = $param->option2 == 'true' && !empty($param->value);

                // Hidden tipi için kullanıcı kontrolü
                $showHiddenCategory = true;
                if ($category->type == 'hidden') {
                    $user = auth()->user();
                    if ($user && $user->customer_id) {
                        // Pivot tablosunda bu kullanıcının firması için kayıt var mı kontrol et
                        $hasAccess = \App\Models\CustomizationParamsCustomersPivot::where([
                            'customer_id' => $user->customer_id,
                            'customization_params_id' => $param->id,
                            'product_id' => $product->id
                        ])->exists();

                        $showHiddenCategory = $hasAccess;
                    } else {
                        // Kullanıcı giriş yapmamış veya customer_id yoksa gizle
                        $showHiddenCategory = false;
                    }
                }
            @endphp

            @if($showHiddenCategory)
            <div class="col-12 col-md-6 col-lg-4 ">
                <div class="form-check radio-with-image">
                    <input class="form-check-input customization-radio"
                           type="radio"
                           name="customizations[0][{{ $category->id }}]"
                           value="{{ $pivot->id }}"
                           id="param_{{ $param->id }}"
                           data-price="{{ $pivot->price ?: 0 }}"
                           data-title="{{ $param->key }}"
                           data-pivot-id="{{ $pivot->id }}"
                           data-has-children="{{ $hasChildren ? 'true' : 'false' }}"
                           data-is-child="false"
                           data-parent-id="0"
                           data-category-id="{{ $category->id }}"
                           data-category-title="{{ $category->title }}">
                    <label class="form-check-label d-flex align-items-start" for="param_{{ $param->id }}">
                        @if($hasImage)
                            <div class="radio-image-container me-3">
                                <img src="{{ asset('storage/' . $param->value) }}"
                                     alt="{{ $param->key }}"
                                     class="radio-option-image"
                                     style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 2px solid #e9ecef;">
                            </div>
                        @endif
                        <div class="radio-content">
                            <div class="fw-bold">{{ $param->key }}</div>
                            @if(($pivot->price ?: 0) > 0)
                                @if(Auth::check() and Auth::user()->roles->contains('id', 3) or Auth::user()->roles->contains('id', 1))
                                <div class="text-success-2">(+{{ number_format($pivot->price ?: 0, 2) }} TL)</div>
                                @endif
                            @endif
                            @if($hasChildren)
                                <small class="text-muted">(Alt seçenekler mevcut)</small>
                            @endif
                        </div>
                    </label>
                </div>
            </div>
            @endif
        @endforeach


    @elseif($category->type == 'checkbox')

        @foreach($categoryParams as $pivot)
            @php
                $param = $pivot->param;
            @endphp
            <div class="col-md-6 mb-2">
                <div class="form-check">
                    <input class="form-check-input customization-checkbox"
                           type="checkbox"
                           name="customizations[0][{{ $category->id }}][]"
                           value="{{ $pivot->id }}"
                           id="param_checkbox_{{ $param->id }}"
                           data-price="{{ $pivot->price ?? 0 }}"
                           data-title="{{ $param->key }}"
                           data-category-id="{{ $category->id }}"
                           data-category-title="{{ $category->title }}">
                    <label class="form-check-label" for="param_checkbox_{{ $param->id }}">
                        {{ $param->key }}
                        @if(($pivot->price ?: 0) > 0)
                            @if(Auth::check() and Auth::user()->roles->contains('id', 3) or Auth::user()->roles->contains('id', 1))
                            <span class="text-success-2">(+{{ number_format($pivot->price ?: 0, 2) }} TL)</span>
                            @endif
                        @endif
                    </label>
                </div>
            </div>
        @endforeach


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
                    <i class="fas fa-info-circle"></i> Sadece sıkıştırılmış dosya yükleyebilirsiniz (.zip, .rar, .7z)
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



        @elseif($category->type == 'select')
        <div class="col-md-6">
            <div class="form-group">

        <select class="form-select customization-select"
                name="customization[{{ $category->id }}]"
                data-category="{{ $category->id }}"
                data-category-title="{{ $category->title }}"
                data-required="{{ $category->required ? 'true' : 'false' }}"
                data-pivot-id="0"
                data-has-children="false">
            <option value="">Seçiniz...</option>
            @foreach($categoryParams as $pivot)
                @php
                    $param = $pivot->param;
                    // Bu parametrenin child'ları var mı kontrol et - customization_params_ust_id pivot ID'si
                    $hasChildren = $product->customizationPivotParams->where('customization_params_ust_id', $pivot->id)->count() > 0;
                    $isChild = $pivot->customization_params_ust_id > 0;
                @endphp
                @if(!$isChild)
                <option value="{{ $pivot->id }}"
                        data-price="{{ $pivot->price ?: 0 }}"
                        data-title="{{ $param->key }}"
                        data-pivot-id="{{ $pivot->id }}"
                        data-has-children="{{ $hasChildren ? 'true' : 'false' }}"
                        data-is-child="{{ $isChild ? 'true' : 'false' }}"
                        data-parent-id="{{ $isChild ? $pivot->customization_params_ust_id : '0' }}"
                        data-category-id="{{ $category->id }}"
                        data-category-title="{{ $category->title }}">
                    {{ $param->key }}
                    @if(($pivot->price ?: 0) > 0)
                        @if(Auth::check() and Auth::user()->roles->contains('id', 3) or Auth::user()->roles->contains('id', 1))
                        (+{{ number_format($pivot->price ?: 0, 2) }} TL)
                        @endif
                    @endif
                </option>
                @endif
            @endforeach
        </select>
        </div>
        </div>

    @endif
@endif
<div class="child-parameters-container"></div>
</div>

@endif
