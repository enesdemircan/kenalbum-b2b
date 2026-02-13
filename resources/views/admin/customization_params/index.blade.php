@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">{{ $category->title }} - Parametreler</h1>
        <p class="page-subtitle">Özelleştirme parametrelerini yönetin</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.customization-categories.index') }}" class="btn-material btn-material-secondary">
            <span class="material-icons">arrow_back</span>
            Kategorilere Dön
        </a>
        <a href="{{ route('admin.customization-params.create', $category->id) }}" class="btn-material btn-material-primary">
            <span class="material-icons">add</span>
            Yeni Parametre Ekle
        </a>
    </div>
</div>

@if(session('success'))
    <div class="material-alert material-alert-success mb-3">
        <span class="material-icons">check_circle</span>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="material-table-wrapper">
    <table class="material-table">
        <thead>
            <tr>
                <th>Key</th>
                <th>Value</th>
                <th>Sıra</th>
                <th>Dosya Yüklemesi</th>
                <th style="width: 140px">İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @foreach($params as $param)
                <tr>
                    <td><code style="background:#f5f5f5;padding:2px 6px;border-radius:4px">{{ $param->key }}</code></td>
                    <td>
                        @if($param->value && Str::startsWith($param->value, 'customization_params/'))
                            <img src="{{ asset('storage/' . $param->value) }}" alt="Resim" width="60" style="border-radius:8px">
                        @else
                            {{ Str::limit($param->value, 30) }}
                        @endif
                    </td>
                    <td>{{ $param->order }}</td>
                    <td>
                        @if($param->option2 == 'true')
                            <span class="material-badge material-badge-success">Evet</span>
                        @else
                            <span class="material-badge material-badge-secondary">Hayır</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1 justify-content-end">
                            <button class="btn-material-icon btn-material-icon-warning" title="Düzenle" onclick="window.location.href='{{ route('admin.customization-params.edit', [$category->id, $param->id]) }}'">
                                <span class="material-icons">edit</span>
                            </button>
                            <form action="{{ route('admin.customization-params.destroy', [$category->id, $param->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-material-icon btn-material-icon-danger" title="Sil">
                                    <span class="material-icons">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="material-pagination">
    {{ $params->links() }}
</div>
@endsection
