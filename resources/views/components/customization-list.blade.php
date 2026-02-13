@props(['customizations', 'totalCustomizationPrice' => 0])

@php
    // Eğer total_customization_price yoksa hesapla
    if($totalCustomizationPrice == 0) {
        $totalCustomizationPrice = 0;
        foreach($customizations as $customization) {
            if($customization['type'] == 'radio' || $customization['type'] == 'hidden' || $customization['type'] == 'select') {
                if(isset($customization['value']) && $customization['value']) {
                    $pivotParam = App\Models\CustomizationPivotParam::find($customization['value']);
                    if($pivotParam && $pivotParam->price) {
                        $totalCustomizationPrice += $pivotParam->price;
                    }
                }
            } elseif($customization['type'] == 'checkbox') {
                if(isset($customization['values']) && is_array($customization['values'])) {
                    foreach($customization['values'] as $pivotId) {
                        $pivotParam = App\Models\CustomizationPivotParam::find($pivotId);
                        if($pivotParam && $pivotParam->price) {
                            $totalCustomizationPrice += $pivotParam->price;
                        }
                    }
                } 
            }
        }
    }
@endphp

<ul style="margin:0px;" class="list-unstyled">
    @foreach($customizations as $categoryId => $customization)
        @php
            $category = App\Models\CustomizationCategory::find($categoryId);
        @endphp
        
        @if($category)
            <li class="mb-1">
           
                @if($customization['type'] == 'files')
                <small class="text-primary fw-medium">{{ $category->title }}:</small>
                
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
                    <x-customization-display :customization="$customization" :category="$category" />
                @endif
            </li>
        @endif
    @endforeach
     
</ul> 