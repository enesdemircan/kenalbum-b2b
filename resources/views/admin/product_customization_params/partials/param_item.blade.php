@php
    // Bu parametrenin alt parametrelerini bul
    $children = $allParams->where('customization_params_ust_id', $param->params_id);
    $hasChildren = $children->count() > 0;
    $levelClass = 'level-' . $level;
    $indentClass = 'ms-' . ($level * 3);
@endphp

<div class="accordion-item {{ $levelClass }}">
    <h2 class="accordion-header" id="heading{{ $param->id }}">
        <button class="accordion-button {{ $index === 0 && $level === 0 ? '' : 'collapsed' }}" type="button" 
                data-bs-toggle="collapse" data-bs-target="#collapse{{ $param->id }}" 
                aria-expanded="{{ $index === 0 && $level === 0 ? 'true' : 'false' }}" 
                aria-controls="collapse{{ $param->id }}">
            <div class="d-flex justify-content-between align-items-center w-100 me-3">
                <div class="{{ $indentClass }}">
                    <strong>{{ $param->param->key }}</strong>
                    <span class="badge bg-info ms-2">{{ $param->param->category->title ?? 'Bilinmiyor' }}</span>
                    @if($param->price)
                        <span class="badge bg-success ms-1">+{{ $param->price }} TL</span>
                    @endif
                    @if($level > 0)
                        <span class="badge bg-secondary ms-1">Seviye {{ $level + 1 }}</span>
                    @endif
                </div>
                <div>
                    @if($hasChildren)
                        <span class="badge bg-secondary">{{ $children->count() }} alt parametre</span>
                    @else
                        <span class="badge bg-light text-dark">Son seviye</span>
                    @endif
                </div>
            </div>
        </button>
    </h2>
    <div id="collapse{{ $param->id }}" class="accordion-collapse collapse {{ $index === 0 && $level === 0 ? 'show' : '' }}" 
         aria-labelledby="heading{{ $param->id }}" data-bs-parent="#customizationParamsAccordion">
        <div class="accordion-body">
            <!-- Mevcut parametre bilgileri -->
            <div class="row mb-3 p-3 bg-light rounded">
                <div class="col-md-6">
                    <strong>{{ $level === 0 ? 'Ana' : 'Alt' }} Parametre:</strong> {{ $param->param->key }}
                    @if($param->param->value)
                        <br><small class="text-muted">{{ $param->param->value }}</small>
                    @endif
                    @if($level > 0)
                        <br><small class="text-info">Parent: {{ $param->getParent()->param->key ?? 'Bilinmiyor' }}</small>
                    @endif
                </div>
                <div class="col-md-3">
                    <strong>Ek Fiyat:</strong>
                    @if($param->price)
                        <span class="text-success">+{{ $param->price }} TL</span>
                    @else
                        <span class="text-muted">Ek fiyat yok</span>
                    @endif
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.product-customization-params.edit', [$product->id, $param->id]) }}" 
                       class="btn btn-sm btn-warning">Düzenle</a>
                    <form action="{{ route('admin.product-customization-params.destroy', [$product->id, $param->id]) }}" 
                          method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" 
                                onclick="return confirm('Silmek istediğinize emin misiniz?')">Sil</button>
                    </form>
                </div>
            </div>
            
            <!-- Alt parametreler (recursive) -->
            @if($hasChildren)
                <div class="mt-3">
                    <h6 class="mb-3">
                        <i class="fas fa-sitemap"></i> 
                        Alt Parametreler (Seviye {{ $level + 2 }}):
                    </h6>
                    
                    <!-- Alt parametreler için nested accordion -->
                    <div class="accordion" id="nestedAccordion{{ $param->id }}">
                        @foreach($children as $childIndex => $childParam)
                            @include('admin.product_customization_params.partials.param_item', [
                                'param' => $childParam,
                                'allParams' => $allParams,
                                'level' => $level + 1,
                                'index' => $childIndex,
                                'product' => $product
                            ])
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-center py-3">
                    <p class="text-muted mb-0">
                        @if($level === 0)
                            Bu ana parametre için henüz alt parametre eklenmemiş.
                        @else
                            Bu alt parametre için henüz daha alt parametre eklenmemiş.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div> 