@props(['item', 'showQuantity' => true, 'showDiscountBadge' => true])

@php
    $discountPercentage = $item->calculateDiscount();
    $totalprice= $item->price * $item->quantity;

@endphp

@if ($item->original_price == $item->price)
    <span class="price">{{ number_format($totalprice, 2) }} ₺</span>
@else
   
    <span style="text-decoration: line-through;" class="price-discount">{{ number_format($item->original_price, 2) }} ₺</span><br>
    <span class="price">{{ number_format($totalprice, 2) }} ₺</span>
@endif
