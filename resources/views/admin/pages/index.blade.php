@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Sayfalar</h1>
        <p class="page-subtitle">Statik sayfaları yönetin</p>
    </div>
    <a href="{{ route('admin.pages.create') }}" class="btn-material btn-material-primary">
        <span class="material-icons">add</span>
        Yeni Sayfa Ekle
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
                <th>ID</th>
                <th>Başlık</th>
                <th>Slug</th>
                <th>Açıklama</th>
                <th>Oluşturulma Tarihi</th>
                <th style="width: 140px">İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pages as $page)
                <tr>
                    <td>{{ $page->id }}</td>
                    <td><strong>{{ $page->title }}</strong></td>
                    <td><span class="material-badge material-badge-secondary">{{ $page->slug }}</span></td>
                    <td>{{ Str::limit($page->description, 50) }}</td>
                    <td>{{ $page->created_at->format('d.m.Y H:i') }}</td>
                    <td>
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('admin.pages.show', $page) }}" class="btn-material-icon btn-material-icon-info" title="Görüntüle">
                                <span class="material-icons">visibility</span>
                            </a>
                            <a href="{{ route('admin.pages.edit', $page) }}" class="btn-material-icon btn-material-icon-warning" title="Düzenle">
                                <span class="material-icons">edit</span>
                            </a>
                            <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu sayfayı silmek istediğinizden emin misiniz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-material-icon btn-material-icon-danger" title="Sil">
                                    <span class="material-icons">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Henüz sayfa eklenmemiş.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($pages->hasPages())
<div class="material-pagination">
    {{ $pages->links() }}
</div>
@endif
@endsection
