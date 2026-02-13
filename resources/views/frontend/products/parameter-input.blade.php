@php
    $paramId = $param->id;
    $paramKey = $param->key;
    $paramType = $param->type;
    $paramTitle = $param->title;
    $paramRequired = $param->required ?? false;
    $paramOptions = $param->options ?? [];
@endphp

<div class="mb-3">
    <label for="param_{{ $paramId }}" class="form-label">
        {{ $paramTitle }}
        @if($paramRequired)
            <span class="text-danger">*</span>
        @endif
    </label>

    @switch($paramType)
        @case('select')
            <select name="customizations[{{ $paramKey }}]" id="param_{{ $paramId }}" class="form-select" @if($paramRequired) required @endif>
                <option value="">Seçiniz</option>
                @foreach($paramOptions as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>
            @break

        @case('radio')
        @case('hidden')
            <div class="form-check-group">
                @foreach($paramOptions as $option)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="customizations[{{ $paramKey }}]" 
                               id="param_{{ $paramId }}_{{ $loop->index }}" value="{{ $option }}" 
                               @if($paramRequired && $loop->first) required @endif>
                        <label class="form-check-label" for="param_{{ $paramId }}_{{ $loop->index }}">
                            {{ $option }}
                        </label>
                    </div>
                @endforeach
            </div>
            @break

        @case('checkbox')
            <div class="form-check-group">
                @foreach($paramOptions as $option)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="customizations[{{ $paramKey }}][]" 
                               id="param_{{ $paramId }}_{{ $loop->index }}" value="{{ $option }}">
                        <label class="form-check-label" for="param_{{ $paramId }}_{{ $loop->index }}">
                            {{ $option }}
                        </label>
                    </div>
                @endforeach
            </div>
            @break

        @case('input')
            <input type="text" name="customization_inputs[{{ $paramId }}]" id="param_{{ $paramId }}" 
                   class="form-control" @if($paramRequired) required @endif placeholder="{{ $paramTitle }}">
            @break

        @case('textarea')
            <textarea name="customization_inputs[{{ $paramId }}]" id="param_{{ $paramId }}" 
                      class="form-control" rows="3" @if($paramRequired) required @endif 
                      placeholder="{{ $paramTitle }}"></textarea>
            @break

        @case('file')
            <input type="file" name="customization_files[{{ $paramId }}]" id="param_{{ $paramId }}" 
                   class="form-control" @if($paramRequired) required @endif 
                   accept="image/*,.pdf,.doc,.docx,.zip,.rar,.7z,.tar.gz,.tar.bz2">
            <input type="hidden" name="customization_files_data[{{ $paramId }}]" id="param_{{ $paramId }}_data">
            @break

        @default
            <input type="text" name="customizations[{{ $paramKey }}]" id="param_{{ $paramId }}" 
                   class="form-control" @if($paramRequired) required @endif placeholder="{{ $paramTitle }}">
    @endswitch

    @if($param->description)
        <small class="form-text text-muted">{{ $param->description }}</small>
    @endif
</div> 