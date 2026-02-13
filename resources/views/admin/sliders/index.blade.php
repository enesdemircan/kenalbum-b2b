@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Slider Yönetimi</h1>
        <p class="page-subtitle">Ana sayfa slider görsellerini yönetin</p>
    </div>
    <a href="{{ route('admin.sliders.create') }}" class="btn-material btn-material-primary">
        <span class="material-icons">add</span>
        Yeni Slider Ekle
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
                <th>Resim</th>
                <th>Başlık</th>
                <th>Açıklama</th>
                <th>Link</th>
                <th>Sıra</th>
                <th>Durum</th>
                <th style="width: 140px">İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sliders as $slider)
                <tr>
                    <td>
                        @if($slider->image)
                            <img src="{{ Storage::url($slider->image) }}" alt="{{ $slider->title }}" style="max-width: 100px; max-height: 60px; object-fit: cover; border-radius: 8px;">
                        @else
                            <span class="text-muted">Resim Yok</span>
                        @endif
                    </td>
                    <td><strong>{{ $slider->title ?? 'Başlık Yok' }}</strong></td>
                    <td>{{ Str::limit($slider->description, 50) ?? 'Açıklama Yok' }}</td>
                    <td>
                        @if($slider->link)
                            <a href="{{ $slider->link }}" target="_blank" class="text-primary">{{ Str::limit($slider->link, 30) }}</a>
                        @else
                            <span class="text-muted">Link Yok</span>
                        @endif
                    </td>
                    <td><span class="material-badge material-badge-secondary">{{ $slider->order }}</span></td>
                    <td>
                        <span class="material-badge material-badge-{{ $slider->is_active ? 'success' : 'danger' }}">
                            {{ $slider->is_active ? 'Aktif' : 'Pasif' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="{{ route('admin.sliders.edit', $slider->id) }}" class="btn-material-icon btn-material-icon-warning" title="Düzenle">
                                <span class="material-icons">edit</span>
                            </a>
                            <form action="{{ route('admin.sliders.destroy', $slider->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu slider\'ı silmek istediğinizden emin misiniz?')">
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
                    <td colspan="7" class="text-center py-4 text-muted">Henüz slider eklenmemiş.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($sliders->hasPages())
<div class="material-pagination">
    {{ $sliders->links() }}
</div>
@endif
@endsection
