@extends('admin.layout')

@section('content')
<div class="page-header">
    <h1 class="page-title">Yeni Kargo Metodu</h1>
</div>

<div class="material-card">
    <div class="material-card-body">
        <form method="POST" action="{{ route('admin.shipping-methods.store') }}">
            @include('admin.shipping_methods._form')
        </form>
    </div>
</div>
@endsection
