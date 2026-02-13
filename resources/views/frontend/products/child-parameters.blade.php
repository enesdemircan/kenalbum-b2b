@if($childParams && $childParams->count() > 0)
    <div class="child-params-section mt-3">
        @php
            // Child parametreleri kategorilere göre grupla
            $groupedChildParams = $childParams->groupBy('param.customization_category_id');
        @endphp
        
        @foreach($groupedChildParams as $categoryId => $categoryChildParams)
            @php
                $childCategory = $categoryChildParams->first()->param->category;
                $inputType = $childCategory->type;
            @endphp

            <div class="customization-section" data-category="{{ $childCategory->id }}" data-type="{{ $childCategory->type }}" data-required="{{ $childCategory->required }}">
                <h4>{{ $childCategory->title }}</h4>
                <div class="row">
                    <div class="col">
                        <div class="form-group">   
                        @if($inputType == 'select')
                            @php
                                // Bu kategorideki tüm child parametrelerin hasChildren durumunu kontrol et
                                $anyHasChildren = false;
                                foreach($categoryChildParams as $childPivotParam) {
                                    $hasChildren = $product->customizationPivotParams->where('customization_params_ust_id', $childPivotParam->id)->count() > 0;
                                    if ($hasChildren) {
                                        $anyHasChildren = true;
                                        break;
                                    }
                                }
                            @endphp
                            <select class="form-control child-customization-select"
                                    name="customizations[{{ $parentParamId }}][{{ $childCategory->id }}]"
                                    data-category-id="{{ $childCategory->id }}"
                                    data-category-title="{{ $childCategory->title }}"
                                    data-parent-id="{{ $parentParamId }}"
                                    data-has-children="{{ $anyHasChildren ? 'true' : 'false' }}">
                                <option value="">Seçiniz</option>
                                @foreach($categoryChildParams as $childPivotParam)
                                    @php
                                        $childParam = $childPivotParam->param;
                                        $hasChildren = $product->customizationPivotParams->where('customization_params_ust_id', $childPivotParam->id)->count() > 0;
                                    @endphp
                                    <option value="{{ $childPivotParam->id }}"
                                            data-pivot-id="{{ $childPivotParam->id }}"
                                            data-price="{{ $childPivotParam->price ?? 0 }}"
                                            data-has-children="{{ $hasChildren ? 'true' : 'false' }}"
                                            data-is-child="true"
                                            data-parent-id="{{ $parentParamId }}"
                                            data-category-id="{{ $childCategory->id }}"
                                            data-category-title="{{ $childCategory->title }}">
                                        {{ $childParam->key }}
                                        @if(($childPivotParam->price ?? 0) > 0)
                                            @if(Auth::check() and Auth::user()->roles->contains('id', 3) or Auth::user()->roles->contains('id', 1))
                                            (+{{ number_format($childPivotParam->price ?? 0, 2) }} TL)
                                            @endif
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        @elseif($inputType == 'radio' || $inputType == 'hidden')
                            <div class="customization-options row">
                                @foreach($categoryChildParams as $childPivotParam)
                                    @php
                                        $childParam = $childPivotParam->param;
                                        $hasChildren = $product->customizationPivotParams->where('customization_params_ust_id', $childPivotParam->id)->count() > 0;
                                        $hasImage = $childParam->option2 == 'true' && !empty($childParam->value);

                                        // Hidden tipi için kullanıcı kontrolü
                                        $showHiddenChildCategory = true;
                                        if ($inputType == 'hidden') {
                                            $user = auth()->user();
                                            if ($user && $user->customer_id) {
                                                // Pivot tablosunda bu kullanıcının firması için kayıt var mı kontrol et
                                                $hasAccess = \App\Models\CustomizationParamsCustomersPivot::where([
                                                    'customer_id' => $user->customer_id,
                                                    'customization_params_id' => $childParam->id,
                                                    'product_id' => $product->id
                                                ])->exists();

                                                $showHiddenChildCategory = $hasAccess;
                                            } else {
                                                // Kullanıcı giriş yapmamış veya customer_id yoksa gizle
                                                $showHiddenChildCategory = false;
                                            }
                                        }
                                    @endphp

                                    @if($showHiddenChildCategory)
                                    <div class="col-12 col-md-6 col-lg-2 ">
                                        <div class="form-check radio-with-image">
                                            <input class="form-check-input child-customization-radio"
                                                   type="radio"
                                                   name="customizations[{{ $parentParamId }}][{{ $childCategory->id }}]"
                                                   value="{{ $childPivotParam->id }}"
                                                   id="child_option_{{ $childPivotParam->id }}"
                                                   data-pivot-id="{{ $childPivotParam->id }}"
                                                   data-price="{{ $childPivotParam->price ?? 0 }}"
                                                   data-has-children="{{ $hasChildren ? 'true' : 'false' }}"
                                                   data-is-child="true"
                                                   data-parent-id="{{ $parentParamId }}"
                                                   data-category-id="{{ $childCategory->id }}"
                                                   data-category-title="{{ $childCategory->title }}">
                                            <label class="form-check-label d-flex align-items-start" for="child_option_{{ $childPivotParam->id }}">
                                                @if($hasImage)
                                                    <div class="radio-image-container me-3">
                                                        <img src="{{ asset('storage/' . $childParam->value) }}"
                                                             alt="{{ $childParam->key }}"
                                                             class="radio-option-image"
                                                             style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; border: 2px solid #e9ecef;">
                                                    </div>
                                                @endif
                                                <div class="radio-content">
                                                    <div class="fw-bold">{{ $childParam->key }}</div>
                                                    @if(($childPivotParam->price ?? 0) > 0)
                                                        @if(Auth::check() and Auth::user()->roles->contains('id', 3) or Auth::user()->roles->contains('id', 1))
                                                        <div class="text-success-2">(+{{ number_format($childPivotParam->price ?? 0, 2) }} TL)</div>
                                                        @endif
                                                        @endif
                                                    @if($hasChildren)
                                                        <!-- <small class="text-muted">(Alt seçenekler mevcut)</small> -->
                                                    @endif
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        @elseif($inputType == 'checkbox')
                            @foreach($categoryChildParams as $childPivotParam)
                                @php
                                    $childParam = $childPivotParam->param;
                                @endphp
                                <div class="form-check">
                                    <input class="form-check-input child-customization-checkbox"
                                           type="checkbox"
                                           name="customizations[{{ $parentParamId }}][{{ $childCategory->id }}][]"
                                           value="{{ $childPivotParam->id }}"
                                           id="child_checkbox_{{ $childPivotParam->id }}"
                                           data-pivot-id="{{ $childPivotParam->id }}"
                                           data-price="{{ $childPivotParam->price ?? 0 }}"
                                           data-is-child="true"
                                           data-parent-id="{{ $parentParamId }}"
                                           data-category-id="{{ $childCategory->id }}"
                                           data-category-title="{{ $childCategory->title }}">
                                    <label class="form-check-label" for="child_checkbox_{{ $childPivotParam->id }}">
                                        {{ $childParam->key }}
                                        @if(Auth::check() and Auth::user()->roles->contains('id', 3) or Auth::user()->roles->contains('id', 1))
                                        @if(($childPivotParam->price ?? 0) > 0)
                                            <span class="text-success-2">(+{{ number_format($childPivotParam->price ?? 0, 2) }} TL)</span>
                                        @endif
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        @elseif($inputType == 'input')
                            @foreach($categoryChildParams as $childPivotParam)
                                @php
                                    $childParam = $childPivotParam->param;
                                @endphp
                                <div class="form-group">

                                    <input type="text"
                                           class="form-control child-customization-input"
                                           name="customizations[{{ $parentParamId }}][{{ $childCategory->id }}]"
                                           id="child_input_{{ $childParam->id }}"
                                           data-param-id="{{ $childParam->id }}"
                                           data-title="{{ $childParam->key }}"
                                           data-price="{{ $childPivotParam->price ?? 0 }}"
                                           data-is-child="true"
                                           data-parent-id="{{ $parentParamId }}"
                                           data-category-id="{{ $childCategory->id }}"
                                           data-category-title="{{ $childCategory->title }}"
                                           value=""
                                           @if($childParam->required == 1) required @endif>
                                        @if(Auth::check() and Auth::user()->roles->contains('id', 3) or Auth::user()->roles->contains('id', 1))
                                    @if(($childPivotParam->price ?? 0) > 0)
                                        <small class="text-success-2">(+{{ number_format($childPivotParam->price ?? 0, 2) }} TL)</small>
                                    @endif
                                    @endif
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            <!-- Alt parametreler için ortak container -->
            <div class="child-parameters-container mt-3"></div>
        @endforeach
    </div>

@endif 