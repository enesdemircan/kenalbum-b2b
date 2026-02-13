@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Ürün Kategorileri</h1>
        <p class="page-subtitle">Kategorileri görüntüleyin ve yönetin</p>
    </div>
    <a href="{{ route('admin.main-categories.create') }}" class="btn-material btn-material-primary">
        <span class="material-icons">add</span>
        Yeni Kategori Ekle
    </a>
</div>

@if(session('success'))
    <div class="material-alert material-alert-success">
        <span class="material-icons">check_circle</span>
        <span>{{ session('success') }}</span>
    </div>
@endif

<!-- Kategoriler Tablosu -->
<div class="material-table-wrapper">
    <table class="material-table">
        <thead>
            <tr>
                <th>Başlık</th>
                <th>Üst Kategori</th>
                <th style="width: 150px">İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @foreach($mainCategories as $category)
                <tr>
                    <td><strong>{{ $category->title }}</strong></td>
                    <td>
                        @if($category->parent)
                            <span class="material-badge material-badge-info">{{ $category->parent->title }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1 justify-content-end">
                            <button class="btn-material-icon btn-material-icon-warning" title="Düzenle" onclick="window.location.href='{{ route('admin.main-categories.edit', $category) }}'">
                                <span class="material-icons">edit</span>
                            </button>
                            <form action="{{ route('admin.main-categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Silmek istediğinize emin misiniz?')">
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

<!-- Sayfalama -->
<div class="material-pagination">
    {{ $mainCategories->links() }}
</div>

@endsection
