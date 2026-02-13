@php
    // Pivot ID'si ile child'ları bul
    $children = $allParams->where('customization_params_ust_id', $param->id);
    $hasChildren = $children->count() > 0;
    $levelClass = 'level-' . $level;
@endphp

<div class="tree-item {{ $levelClass }}" data-param-id="{{ $param->id }}">
    <div class="d-flex align-items-center">
        @if($hasChildren)
            <span class="expand-icon expand-toggle">
                <i class="fas fa-chevron-right"></i>
            </span>
        @else
            <span class="level-indicator">
                <i class="fas fa-circle"></i>
            </span>
        @endif
        
        <div class="flex-grow-1">
            <strong>{{ $param->param->key }}</strong>
            @if($param->price)
                <span class="param-badge badge bg-success">+{{ $param->price }} TL</span>
            @endif
            <br>
            <small class="text-muted">{{ $param->param->category->title ?? 'Bilinmiyor' }}</small>
        </div>
        
        @if($hasChildren)
            <span class="badge bg-info">{{ $children->count() }}</span>
        @endif
    </div>
    
    @if($hasChildren)
        <div class="tree-children" style="display: none;">
            @foreach($children as $childParam)
                @include('admin.product_customization_params.partials.tree_item', [
                    'param' => $childParam,
                    'allParams' => $allParams,
                    'level' => $level + 1,
                    'product' => $product
                ])
            @endforeach
        </div>
    @endif
</div> 