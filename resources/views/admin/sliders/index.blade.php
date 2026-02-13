@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Slider Yönetimi</h1>
    <a href="{{ route('admin.sliders.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Yeni Slider Ekle
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Resim</th>
            <th>Başlık</th>
            <th>Açıklama</th>
            <th>Link</th>
            <th>Sıra</th>
            <th>Durum</th>
            <th style="width: 190px;"></th>
        </tr>
    </thead>
    <tbody>
        @forelse($sliders as $slider)
            <tr>
                <td>
                    @if($slider->image)
                        <img src="{{ Storage::url($slider->image) }}" 
                             alt="{{ $slider->title }}" 
                             style="max-width: 100px; max-height: 60px; object-fit: cover;">
                    @else
                        <span class="text-muted">Resim Yok</span>
                    @endif
                </td>
                <td>{{ $slider->title ?? 'Başlık Yok' }}</td>
                <td>{{ Str::limit($slider->description, 50) ?? 'Açıklama Yok' }}</td>
                <td>
                    @if($slider->link)
                        <a href="{{ $slider->link }}" target="_blank">{{ $slider->link }}</a>
                    @else
                        <span class="text-muted">Link Yok</span>
                    @endif
                </td>
                <td>{{ $slider->order }}</td>
                <td>
                    <span class="badge bg-{{ $slider->is_active ? 'success' : 'danger' }}">
                        {{ $slider->is_active ? 'Aktif' : 'Pasif' }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('admin.sliders.edit', $slider->id) }}" class="btn btn-sm btn-warning" title="Düzenle">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <form action="{{ route('admin.sliders.destroy', $slider->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bu slider\'ı silmek istediğinizden emin misiniz?')" title="Sil">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">Henüz slider eklenmemiş.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- Sayfalama -->
<div class="d-flex justify-content-center mt-4">
    {{ $sliders->links() }}
</div>

@endsection 