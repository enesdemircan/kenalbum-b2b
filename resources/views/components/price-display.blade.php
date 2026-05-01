@props(['item', 'showQuantity' => true, 'showDiscountBadge' => true])

@php
    $discountPercentage = $item->calculateDiscount();
    $qty = $showQuantity ? $item->quantity : 1;
    $unitOriginal = (float) $item->original_price;
    $unitDiscounted = (float) $item->price;
    $totalOriginal = $unitOriginal * $qty;
    $totalDiscounted = $unitDiscounted * $qty;
    $hasDiscount = $unitOriginal > $unitDiscounted;
@endphp

@if($hasDiscount)
    <div class="price-block">
        <span class="price-original" style="text-decoration: line-through; color:#9ca3af; font-size:0.85em;">{{ number_format($totalOriginal, 2) }} ₺</span>
        <div class="price fw-bold">{{ number_format($totalDiscounted, 2) }} ₺</div>
        @if($showDiscountBadge && $discountPercentage > 0)
            <span class="badge bg-success-subtle text-success" style="font-size:0.75em;">%{{ number_format($discountPercentage, 0) }} indirim</span>
        @endif
    </div>
@else
    <span class="price fw-bold">{{ number_format($totalDiscounted, 2) }} ₺</span>
@endif
