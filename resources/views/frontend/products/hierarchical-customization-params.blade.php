@if($hierarchicalParams->count() > 0)
    <div class="customization-params">
        <h4>Özelleştirme Seçenekleri</h4>
        
        @foreach($hierarchicalParams as $categoryData)
            <div class="customization-category mb-4">
                @php
                    $pivot = $categoryData['pivot'];
                    $param = $categoryData['param'];
                    $children = $categoryData['children'];
                @endphp
                
                <div class="category-header">
                    <h5 class="mb-3">
                        @if($param->category)
                            <i class="fas fa-tag"></i> {{ $param->category->title }}
                        @endif
                        - {{ $param->title }}
                    </h5>
                </div>
                
                <div class="category-content">
                    @if($param->type === 'select')
                        <div class="form-group">
                            <select name="customization[{{ $pivot->id }}]" 
                                    class="form-control customization-select" 
                                    data-pivot-id="{{ $pivot->id }}"
                                    data-price="{{ $pivot->price ?? 0 }}">
                                <option value="">Seçiniz</option>
                                @if($param->options)
                                    @foreach(explode(',', $param->options) as $option)
                                        <option value="{{ trim($option) }}">{{ trim($option) }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    @elseif($param->type === 'text')
                        <div class="form-group">
                            <input type="text" 
                                   name="customization[{{ $pivot->id }}]" 
                                   class="form-control customization-text"
                                   data-pivot-id="{{ $pivot->id }}"
                                   data-price="{{ $pivot->price ?? 0 }}"
                                   placeholder="{{ $param->title }}">
                        </div>
                    @elseif($param->type === 'textarea')
                        <div class="form-group">
                            <textarea name="customization[{{ $pivot->id }}]" 
                                      class="form-control customization-textarea"
                                      data-pivot-id="{{ $pivot->id }}"
                                      data-price="{{ $pivot->price ?? 0 }}"
                                      rows="3"
                                      placeholder="{{ $param->title }}"></textarea>
                        </div>
                    @elseif($param->type === 'checkbox')
                        <div class="form-check">
                            <input type="checkbox" 
                                   name="customization[{{ $pivot->id }}]" 
                                   class="form-check-input customization-checkbox"
                                   data-pivot-id="{{ $pivot->id }}"
                                   data-price="{{ $pivot->price ?? 0 }}"
                                   value="1">
                            <label class="form-check-label">
                                {{ $param->title }}
                                @if($pivot->price > 0)
                                    <span class="text-muted">(+{{ $pivot->price }} TL)</span>
                                @endif
                            </label>
                        </div>
                    @elseif($param->type === 'radio')
                        @if($param->options)
                            @foreach(explode(',', $param->options) as $option)
                                <div class="form-check">
                                    <input type="radio" 
                                           name="customization[{{ $pivot->id }}]" 
                                           class="form-check-input customization-radio"
                                           data-pivot-id="{{ $pivot->id }}"
                                           data-price="{{ $pivot->price ?? 0 }}"
                                           value="{{ trim($option) }}">
                                    <label class="form-check-label">
                                        {{ trim($option) }}
                                        @if($pivot->price > 0)
                                            <span class="text-muted">(+{{ $pivot->price }} TL)</span>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        @endif
                    @endif
                </div>
                
                <!-- Alt kategorileri recursive olarak göster -->
                @if($children->count() > 0)
                    <div class="sub-categories mt-3" style="margin-left: 20px;">
                        @foreach($children as $childData)
                            @php
                                $childPivot = $childData['pivot'];
                                $childParam = $childData['param'];
                                $grandChildren = $childData['children'];
                            @endphp
                            
                            <div class="sub-category mb-3">
                                <div class="sub-category-header">
                                    <h6 class="mb-2">
                                        @if($childParam->category)
                                            <i class="fas fa-tag"></i> {{ $childParam->category->title }}
                                        @endif
                                        - {{ $childParam->title }}
                                    </h6>
                                </div>
                                
                                <div class="sub-category-content">
                                    @if($childParam->type === 'select')
                                        <div class="form-group">
                                            <select name="customization[{{ $childPivot->id }}]" 
                                                    class="form-control customization-select" 
                                                    data-pivot-id="{{ $childPivot->id }}"
                                                    data-price="{{ $childPivot->price ?? 0 }}">
                                                <option value="">Seçiniz</option>
                                                @if($childParam->options)
                                                    @foreach(explode(',', $childParam->options) as $option)
                                                        <option value="{{ trim($option) }}">{{ trim($option) }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    @elseif($childParam->type === 'text')
                                        <div class="form-group">
                                            <input type="text" 
                                                   name="customization[{{ $childPivot->id }}]" 
                                                   class="form-control customization-text"
                                                   data-pivot-id="{{ $childPivot->id }}"
                                                   data-price="{{ $childPivot->price ?? 0 }}"
                                                   placeholder="{{ $childParam->title }}">
                                        </div>
                                    @elseif($childParam->type === 'textarea')
                                        <div class="form-group">
                                            <textarea name="customization[{{ $childPivot->id }}]" 
                                                      class="form-control customization-textarea"
                                                      data-pivot-id="{{ $childPivot->id }}"
                                                      data-price="{{ $childPivot->price ?? 0 }}"
                                                      rows="3"
                                                      placeholder="{{ $childParam->title }}"></textarea>
                                        </div>
                                    @elseif($childParam->type === 'checkbox')
                                        <div class="form-check">
                                            <input type="checkbox" 
                                                   name="customization[{{ $childPivot->id }}]" 
                                                   class="form-check-input customization-checkbox"
                                                   data-pivot-id="{{ $childPivot->id }}"
                                                   data-price="{{ $childPivot->price ?? 0 }}"
                                                   value="1">
                                            <label class="form-check-label">
                                                {{ $childParam->title }}
                                                @if($childPivot->price > 0)
                                                    <span class="text-muted">(+{{ $childPivot->price }} TL)</span>
                                                @endif
                                            </label>
                                        </div>
                                    @elseif($childParam->type === 'radio' || $childParam->type === 'hidden')
                                        @if($childParam->options)
                                            @foreach(explode(',', $childParam->options) as $option)
                                                <div class="form-check">
                                                    <input type="radio" 
                                                           name="customization[{{ $childPivot->id }}]" 
                                                           class="form-check-input customization-radio"
                                                           data-pivot-id="{{ $childPivot->id }}"
                                                           data-price="{{ $childPivot->price ?? 0 }}"
                                                           value="{{ trim($option) }}">
                                                    <label class="form-check-label">
                                                        {{ trim($option) }}
                                                        @if($childPivot->price > 0)
                                                            <span class="text-muted">(+{{ $childPivot->price }} TL)</span>
                                                        @endif
                                                    </label>
                                                </div>
                                            @endforeach
                                        @endif
                                    @endif
                                </div>
                                
                                <!-- Daha derin alt kategoriler için recursive çağrı -->
                                @if($grandChildren->count() > 0)
                                    <div class="sub-sub-categories mt-2" style="margin-left: 20px;">
                                        @foreach($grandChildren as $grandChildData)
                                            @php
                                                $grandChildPivot = $grandChildData['pivot'];
                                                $grandChildParam = $grandChildData['param'];
                                            @endphp
                                            
                                            <div class="sub-sub-category mb-2">
                                                <div class="sub-sub-category-header">
                                                    <small class="text-muted">
                                                        @if($grandChildParam->category)
                                                            <i class="fas fa-tag"></i> {{ $grandChildParam->category->title }}
                                                        @endif
                                                        - {{ $grandChildParam->title }}
                                                    </small>
                                                </div>
                                                
                                                <div class="sub-sub-category-content">
                                                    @if($grandChildParam->type === 'select')
                                                        <div class="form-group">
                                                            <select name="customization[{{ $grandChildPivot->id }}]" 
                                                                    class="form-control form-control-sm customization-select" 
                                                                    data-pivot-id="{{ $grandChildPivot->id }}"
                                                                    data-price="{{ $grandChildPivot->price ?? 0 }}">
                                                                <option value="">Seçiniz</option>
                                                                @if($grandChildParam->options)
                                                                    @foreach(explode(',', $grandChildParam->options) as $option)
                                                                        <option value="{{ trim($option) }}">{{ trim($option) }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        </div>
                                                    @elseif($grandChildParam->type === 'text')
                                                        <div class="form-group">
                                                            <input type="text" 
                                                                   name="customization[{{ $grandChildPivot->id }}]" 
                                                                   class="form-control form-control-sm customization-text"
                                                                   data-pivot-id="{{ $grandChildPivot->id }}"
                                                                   data-price="{{ $grandChildPivot->price ?? 0 }}"
                                                                   placeholder="{{ $grandChildParam->title }}">
                                                        </div>
                                                    @elseif($grandChildParam->type === 'checkbox')
                                                        <div class="form-check">
                                                            <input type="checkbox" 
                                                                   name="customization[{{ $grandChildPivot->id }}]" 
                                                                   class="form-check-input customization-checkbox"
                                                                   data-pivot-id="{{ $grandChildPivot->id }}"
                                                                   data-price="{{ $grandChildPivot->price ?? 0 }}"
                                                                   value="1">
                                                            <label class="form-check-label">
                                                                {{ $grandChildParam->title }}
                                                                @if($grandChildPivot->price > 0)
                                                                    <span class="text-muted">(+{{ $grandChildPivot->price }} TL)</span>
                                                                @endif
                                                            </label>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endif 