@extends('admin.layout')

@section('content')
<div class="page-header">
    <h1 class="page-title">Kargo Metodunu Düzenle</h1>
</div>

<div class="material-card">
    <div class="material-card-body">
        <form method="POST" action="{{ route('admin.shipping-methods.update', $method->id) }}">
            @method('PUT')
            @include('admin.shipping_methods._form')
        </form>
    </div>
</div>
@endsection
