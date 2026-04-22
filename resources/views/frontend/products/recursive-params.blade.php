@foreach($params as $paramData)
    @php
        $pivot = $paramData['pivot'];
        $param = $paramData['param'];
        $children = $paramData['children'];
        $level = $level ?? 0;
        $prefix = str_repeat('-', $level);
        $uniqueId = 'param-' . $pivot->id . '-' . $level;
    @endphp
    

    

    
    <div class="param-level-{{ $level }} mb-{{ $level == 0 ? 4 : ($level == 1 ? 3 : ($level == 2 ? 2 : 1)) }}"  >
        
        <div class="param-header">
            <h4 >{{ $param->category->title }}</h4>
           
        </div>
        
        <!-- Tüm parametreler için input alanları -->
        <div class="param-inputs mb-3">
            @if($param->category && ($param->category->type === 'radio' || $param->category->type === 'hidden'))
                <div class="radio-group">
                    @if($param->option1)
                        @php
                            $options = explode(',', $param->option1);
                        @endphp
                        @foreach($options as $option)
                            <div class="form-check">
                                <input type="radio" 
                                       name="customization[{{ $pivot->id }}]" 
                                       class="form-check-input customization-radio"
                                       data-pivot-id="{{ $pivot->id }}"
                                       data-price="{{ $pivot->price ?? 0 }}"
                                       value="{{ trim($option) }}"
                                       id="radio-{{ $pivot->id }}-{{ $loop->index }}"
                                       data-children-id="{{ $uniqueId }}">
                                <label class="form-check-label" for="radio-{{ $pivot->id }}-{{ $loop->index }}">
                                    {{ trim($option) }}
                                    @if($pivot->price > 0)
                                        <span class="text-muted">(+{{ $pivot->price }} TL)</span>
                                    @endif
                                </label>
                            </div>
                        @endforeach
                    @else
                        <!-- option1 boşsa, param->key'i radio button olarak göster -->
                        <div class="form-check">
                            <input type="radio" 
                                   name="customization[{{ $pivot->id }}]" 
                                   class="form-check-input customization-radio"
                                   data-pivot-id="{{ $pivot->id }}"
                                   data-price="{{ $pivot->price ?? 0 }}"
                                   value="{{ $param->key }}"
                                   id="radio-{{ $pivot->id }}"
                                   data-children-id="{{ $uniqueId }}">
                            <label class="form-check-label" for="radio-{{ $pivot->id }}">
                                {{ $param->key }}
                                @if($pivot->price > 0)
                                    <span class="text-muted">(+{{ $pivot->price }} TL)</span>
                                @endif
                            </label>
                        </div>
                    @endif
                </div>
            @elseif($param->category && $param->category->type === 'checkbox')
                <div class="checkbox-group">
                    <div class="form-check">
                        <input type="checkbox" 
                               name="customization[{{ $pivot->id }}]" 
                               class="form-check-input customization-checkbox"
                               data-pivot-id="{{ $pivot->id }}"
                               data-price="{{ $pivot->price ?? 0 }}"
                               value="1"
                               id="checkbox-{{ $pivot->id }}">
                        <label class="form-check-label" for="checkbox-{{ $pivot->id }}">
                            {{ $param->key }}
                            @if($pivot->price > 0)
                                <span class="text-muted">(+{{ $pivot->price }} TL)</span>
                            @endif
                        </label>
                    </div>
                </div>
            @elseif($param->category && $param->category->type === 'select')
                <div class="select-group">
                    <select name="customization[{{ $pivot->id }}]" 
                            class="form-control customization-select" 
                            data-pivot-id="{{ $pivot->id }}"
                            data-price="{{ $pivot->price ?? 0 }}">
                        <option value="">Seçiniz</option>
                        @if($param->option1)
                            @php
                                $options = explode(',', $param->option1);
                            @endphp
                            @foreach($options as $option)
                                <option value="{{ trim($option) }}">{{ trim($option) }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            @elseif($param->category && $param->category->type === 'text')
                <div class="text-group">
                    <input type="text" 
                           name="customization[{{ $pivot->id }}]" 
                           class="form-control customization-text"
                           data-pivot-id="{{ $pivot->id }}"
                           data-price="{{ $pivot->price ?? 0 }}"
                           placeholder="{{ $param->key }}">
                </div>
            @elseif($param->category && $param->category->type === 'textarea')
                <div class="textarea-group">
                    <textarea name="customization[{{ $pivot->id }}]" 
                              class="form-control customization-textarea"
                              data-pivot-id="{{ $pivot->id }}"
                              data-price="{{ $pivot->price ?? 0 }}"
                              rows="3"
                              placeholder="{{ $param->key }}"></textarea>
                </div>
            @elseif($param->category && $param->category->type === 'file')
                <div class="file-group">
                    <input type="file"
                           name="customization[{{ $pivot->id }}]"
                           class="form-control customization-file"
                           data-pivot-id="{{ $pivot->id }}"
                           data-price="{{ $pivot->price ?? 0 }}">
                    <small class="text-muted d-block mt-1">
                        <i class="fas fa-info-circle"></i> Maksimum dosya boyutu: <strong>500 MB</strong> (toplam)
                    </small>
                </div>
            @elseif($param->category && $param->category->type === 'files')
                <div class="files-group">
                    <label class="form-label fw-bold">
                        <i class="fas fa-file-archive"></i> {{ $param->key }}
                    </label>
                    <input type="file"
                           name="customization[{{ $pivot->id }}]"
                           class="form-control customization-zip-file"
                           data-pivot-id="{{ $pivot->id }}"
                           data-price="{{ $pivot->price ?? 0 }}"
                           accept=".zip,.rar,.7z">
                    <small class="text-muted d-block mt-1">
                        <i class="fas fa-info-circle"></i> Sadece sıkıştırılmış dosya (.zip, .rar, .7z) — Maksimum: <strong>500 MB</strong>
                    </small>
                </div>
            @endif
        </div>
        
        @if($children->count() > 0)
            <div class="param-children" id="{{ $uniqueId }}" style="display: none;">
                @include('frontend.products.recursive-params', [
                    'params' => $children,
                    'level' => $level + 1
                ])
            </div>
        @endif
    </div>
@endforeach 