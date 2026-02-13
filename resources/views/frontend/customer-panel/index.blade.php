@extends('frontend.layout')

@section('content')
    <h1>Müşteri Paneli</h1>
    <p>Hoş geldiniz, {{ auth()->user()->name }}!</p>
    <h3>Siparişleriniz</h3>
    @if(count($orders) > 0)
        <ul>
            @foreach($orders as $order)
                <li>{{ $order }}</li>
            @endforeach
        </ul>
    @else
        <p>Henüz siparişiniz yok.</p>
    @endif
@endsection 