@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">{{ $category->title }} - Parametreler</h1>
    <a href="{{ route('admin.customization-params.create', $category->id) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Yeni Parametre Ekle
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Key</th>
            <th>Value</th>
            <th>Sıra</th>
            <th>Dosya Yüklemesi</th>
            <th style="width: 190px;"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($params as $param)
            <tr>
                <td>{{ $param->id }}</td>
                <td>{{ $param->key }}</td>
                <td>
                    @if($param->value && Str::startsWith($param->value, 'customization_params/'))
                        <img src="{{ asset('storage/' . $param->value) }}" alt="Resim" width="60">
                    @else
                        {{ $param->value }}
                    @endif
                </td>
                <td>{{ $param->order }}</td>
                <td>
                    @if($param->option2 == 'true')
                        <span class="badge bg-success">Evet</span>
                    @else
                        <span class="badge bg-secondary">Hayır</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.customization-params.edit', [$category->id, $param->id]) }}" class="btn btn-sm btn-warning" title="Düzenle">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <form action="{{ route('admin.customization-params.destroy', [$category->id, $param->id]) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Silmek istediğinize emin misiniz?')" title="Sil">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Sayfalama -->
<div class="d-flex justify-content-center mt-4">
    {{ $params->links() }}
</div>

@endsection 