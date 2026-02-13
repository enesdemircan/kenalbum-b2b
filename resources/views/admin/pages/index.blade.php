@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Sayfalar</h1>
    <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Yeni Sayfa Ekle
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Başlık</th>
            <th>Slug</th>
            <th>Açıklama</th>
            <th>Oluşturulma Tarihi</th>
            <th style="width: 190px;"></th>
        </tr>
    </thead>
    <tbody>
        @forelse($pages as $page)
            <tr>
                <td>{{ $page->id }}</td>
                <td>{{ $page->title }}</td>
                <td><code>{{ $page->slug }}</code></td>
                <td>{{ Str::limit($page->description, 50) }}</td>
                <td>{{ $page->created_at->format('d.m.Y H:i') }}</td>
                <td>
                    <a href="{{ route('admin.pages.show', $page) }}" class="btn btn-sm btn-info" title="Görüntüle">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-sm btn-warning" title="Düzenle">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bu sayfayı silmek istediğinizden emin misiniz?')" title="Sil">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center">Henüz sayfa eklenmemiş.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- Sayfalama -->
<div class="d-flex justify-content-center mt-4">
    {{ $pages->links() }}
</div>

@endsection 