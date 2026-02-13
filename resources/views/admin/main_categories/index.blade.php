@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Ürün Kategorileri</h1>
    <a href="{{ route('admin.main-categories.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Yeni Kategori Ekle
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered table-striped">
    <thead>
        <tr>
         
            <th>Başlık</th>
            <th>Üst Kategori</th>
            
            <th style="width: 190px;"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($mainCategories as $category)
            <tr>
                <td>{{ $category->title }}</td>
                <td>{{ $category->parent ? $category->parent->title : '-' }}</td>
              
                <td>
                    <a href="{{ route('admin.main-categories.edit', $category) }}" class="btn btn-sm btn-warning" title="Düzenle">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <form action="{{ route('admin.main-categories.destroy', $category) }}" method="POST" class="d-inline">
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
    {{ $mainCategories->links() }}
</div>

@endsection 