@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Özelleştirme Kategorileri</h1>
        <p class="page-subtitle">Özelleştirme kategorilerini görüntüleyin ve yönetin</p>
    </div>
    <a href="{{ route('admin.customization-categories.create') }}" class="btn-material btn-material-primary">
        <span class="material-icons">add</span>
        Yeni Kategori Ekle
    </a>
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
                <th>Başlık</th>
                <th>Tip</th>
                <th>Üst Kategori</th>
                <th>Sıra</th>
                <th style="width: 180px">İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
                <tr>
                    <td>{{ $category->title }}</td>
                    <td><span class="material-badge material-badge-secondary">{{ $category->type }}</span></td>
                    <td>{{ $category->parent ? $category->parent->title : '-' }}</td>
                    <td>{{ $category->order }}</td>
                    <td>
                        <div class="d-flex gap-1 justify-content-end">
                            <button class="btn-material-icon btn-material-icon-warning" title="Düzenle" onclick="window.location.href='{{ route('admin.customization-categories.edit', $category->id) }}'">
                                <span class="material-icons">edit</span>
                            </button>
                            <button class="btn-material-icon btn-material-icon-info" title="Parametreler" onclick="window.location.href='{{ route('admin.customization-params.index', ['category' => $category->id]) }}'">
                                <span class="material-icons">tune</span>
                            </button>
                            <form action="{{ route('admin.customization-categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
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
    {{ $categories->links() }}
</div>
@endsection
