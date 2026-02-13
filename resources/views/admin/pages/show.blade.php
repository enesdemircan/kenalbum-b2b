@extends('admin.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ $page->title }}</h1>
            <div>
                <a href="{{ route('admin.pages.edit', $page) }}" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Düzenle
                </a>
                <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Geri
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Sayfa Detayları</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">ID:</th>
                                <td>{{ $page->id }}</td>
                            </tr>
                            <tr>
                                <th>Başlık:</th>
                                <td>{{ $page->title }}</td>
                            </tr>
                            <tr>
                                <th>Slug:</th>
                                <td><code>{{ $page->slug }}</code></td>
                            </tr>
                            <tr>
                                <th>Açıklama:</th>
                                <td>{{ $page->description ?: 'Açıklama yok' }}</td>
                            </tr>
                            <tr>
                                <th>Oluşturulma:</th>
                                <td>{{ $page->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Güncellenme:</th>
                                <td>{{ $page->updated_at->format('d.m.Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">İçerik</h5>
            </div>
            <div class="card-body">
                <div class="page-content">
                    {!! $page->text !!}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.page-content {
    line-height: 1.6;
}

.page-content h1, .page-content h2, .page-content h3, 
.page-content h4, .page-content h5, .page-content h6 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}

.page-content p {
    margin-bottom: 1rem;
}

.page-content ul, .page-content ol {
    margin-bottom: 1rem;
    padding-left: 2rem;
}

.page-content blockquote {
    border-left: 4px solid #dee2e6;
    padding-left: 1rem;
    margin: 1rem 0;
    font-style: italic;
    color: #6c757d;
}

.page-content table {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0;
}

.page-content table th,
.page-content table td {
    border: 1px solid #dee2e6;
    padding: 0.5rem;
}

.page-content table th {
    background-color: #f8f9fa;
    font-weight: bold;
}

.page-content img {
    max-width: 100%;
    height: auto;
    margin: 1rem 0;
}
</style>
@endsection 