@props(['customization', 'category'])

@php
    $type = $customization['type'] ?? '';
    $value = $customization['value'] ?? '';
    $values = $customization['values'] ?? [];
    $totalPrice = 0;
@endphp

<small class="text-primary fw-medium">{{ $category->title }}:</small>

@if($type == 'radio' || $type == 'hidden')
    @php
        $pivotParam = null;
        $isNumericValue = is_numeric($customization['value']);
        
        if(isset($customization['value']) && $customization['value'] && $isNumericValue) {
            $pivotParam = App\Models\CustomizationPivotParam::with('param')->find($customization['value']);
        }
    @endphp
     
    @if($pivotParam && $pivotParam->param)
        <small class="text-muted">{{ $pivotParam->param->key }}</small>
        @if($pivotParam->price > 0)
            @php $totalPrice += $pivotParam->price; @endphp
            @canSeePrices
              
            <span class="badge badge-success" style="color: #27ae60; font-size: 0.8em;">(+{{ number_format($pivotParam->price, 2) }} ₺)</span>
            @endif
            @endif
    @elseif(isset($customization['value']) && $customization['value'] && !$isNumericValue)
        {{-- Input alanı için (string değer) --}}
        <small class="text-muted">{{ $customization['value'] }}</small>
    @else
        <small class="text-muted">Seçim yapılmadı</small>
    @endif

@elseif($type == 'select')
    @php
        $pivotParam = null;
        if(isset($customization['value']) && $customization['value']) {
            $pivotParam = App\Models\CustomizationPivotParam::with('param')->find($customization['value']);
        }
    @endphp
    
    @if($pivotParam && $pivotParam->param)
        <small class="text-muted">{{ $pivotParam->param->key }}</small>
        @if($pivotParam->price > 0)
            @php $totalPrice += $pivotParam->price; @endphp
            @canSeePrices
              
            <span class="badge badge-success" style="color: #27ae60; font-size: 0.8em;">(+{{ number_format($pivotParam->price, 2) }} ₺)</span>
            @endif
            @endif
    @else
        <small class="text-muted">Seçim yapılmadı</small>
    @endif

@elseif($type == 'checkbox')
    @if(isset($customization['values']) && is_array($customization['values']))
        <small class="text-muted">{{ count($customization['values']) }} seçenek seçildi</small>
        @php
            $totalCheckboxPrice = 0;
            foreach($customization['values'] as $pivotId) {
                $pivotParam = App\Models\CustomizationPivotParam::find($pivotId);
                if($pivotParam && $pivotParam->price) {
                    $totalCheckboxPrice += $pivotParam->price;
                }
            }
            $totalPrice += $totalCheckboxPrice;
        @endphp
        @if($totalCheckboxPrice > 0)
        @canSeePrices
              
            <span class="badge badge-success" style="color: #27ae60; font-size: 0.8em;">(+{{ number_format($totalCheckboxPrice, 2) }} ₺)</span>
            @endif
            @endif
    @else
        <small class="text-muted">Seçim yapılmadı</small>
    @endif

@elseif($type == 'input')
    @php
        $inputValue = $customization['value'] ?? '';
        if (is_array($inputValue)) {
            $inputValue = json_encode($inputValue);
        }
        // Boş değerleri kontrol et
        $isEmpty = empty($inputValue) || $inputValue === 'null' || $inputValue === '{"data":null}';
    @endphp
    @if(!$isEmpty)
        <small class="text-muted">{{ $inputValue }}</small>
    @else
        <small class="text-muted">Girilmedi</small>
    @endif

@elseif($type == 'files')
    @if(isset($customization['file_path']))
        <small class="text-muted">Dosya yüklendi</small>
        @if(isset($customization['s3_path']))
            @if(auth()->user()->hasRole(['administrator', 'editor']))
                <a href="{{ route('admin.download.customization', ['path' => base64_encode($customization['s3_path'])]) }}" target="_blank" class="badge badge-info" style="color: #4e12da; font-size: 0.8em;">İndir</a>
            @else
                <a href="{{ route('download.customization', ['path' => base64_encode($customization['s3_path'])]) }}" target="_blank" class="badge badge-info" style="color: #4e12da; font-size: 0.8em;">İndir</a>
            @endif
        @elseif(isset($customization['s3_url']))
            @if(auth()->user()->hasRole(['administrator', 'editor']))
                <a href="{{ route('admin.download.customization', ['path' => base64_encode(str_replace('https://kenalbum.fra1.digitaloceanspaces.com/', '', $customization['s3_url']))]) }}" target="_blank" class="badge badge-info" style="color: #4e12da; font-size: 0.8em;">İndir</a>
            @else
                <a href="{{ route('download.customization', ['path' => base64_encode(str_replace('https://kenalbum.fra1.digitaloceanspaces.com/', '', $customization['s3_url']))]) }}" target="_blank" class="badge badge-info" style="color: #4e12da; font-size: 0.8em;">İndir</a>
            @endif
        @else
            @if(auth()->user()->hasRole(['administrator', 'editor']))
                <a href="{{ asset($customization['file_path']) }}" target="_blank" class="badge badge-info" style="color: #4e12da; font-size: 0.8em;">İndir</a>
            @else
                <a href="{{ route('download.customization', ['path' => base64_encode($customization['file_path'])]) }}" target="_blank" class="badge badge-info" style="color: #4e12da; font-size: 0.8em;">İndir</a>
            @endif
        @endif
    @elseif(isset($customization['processing']) && $customization['processing'])
        <small class="text-muted">Dosya işleniyor...</small>
    @else
        <small class="text-muted">Dosya seçildi</small>
    @endif

@else
    <small class="text-muted">Bilinmeyen tip</small>
@endif

@php
    // Component'ten fiyat bilgisini döndür (opsiyonel)
    if(isset($totalPrice) && $totalPrice > 0) {
        // Bu değer component dışında kullanılabilir
    }
@endphp 