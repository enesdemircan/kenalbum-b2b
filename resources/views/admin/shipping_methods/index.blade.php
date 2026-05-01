@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Kargo Metotları</h1>
        <p class="page-subtitle">Sipariş tamamlama adımında müşteriye gösterilecek kargo seçenekleri</p>
    </div>
    <a href="{{ route('admin.shipping-methods.create') }}" class="btn-material btn-material-primary">
        <span class="material-icons">add</span>
        Yeni Kargo Ekle
    </a>
</div>

@if(session('success'))
    <div class="material-alert material-alert-success">
        <span class="material-icons">check_circle</span>
        <span>{{ session('success') }}</span>
    </div>
@endif
@if(session('error'))
    <div class="material-alert material-alert-danger">
        <span class="material-icons">error</span>
        <span>{{ session('error') }}</span>
    </div>
@endif

<div class="material-table-wrapper">
    <table class="material-table">
        <thead>
            <tr>
                <th>Sıra</th>
                <th>Başlık</th>
                <th>Kod</th>
                <th>Ücret</th>
                <th>Açıklama</th>
                <th>Aktif</th>
                <th style="width:160px">İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @forelse($methods as $m)
                <tr>
                    <td>{{ $m->sort_order }}</td>
                    <td><strong>{{ $m->title }}</strong></td>
                    <td><code>{{ $m->code }}</code></td>
                    <td>{{ number_format($m->price, 2) }} ₺</td>
                    <td class="text-muted small">{{ $m->description }}</td>
                    <td>
                        @if($m->is_active)
                            <span class="material-badge material-badge-success">Aktif</span>
                        @else
                            <span class="material-badge material-badge-secondary">Pasif</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1 justify-content-end">
                            <button class="btn-material-icon btn-material-icon-warning" title="Düzenle" onclick="window.location.href='{{ route('admin.shipping-methods.edit', $m->id) }}'">
                                <span class="material-icons">edit</span>
                            </button>
                            <form action="{{ route('admin.shipping-methods.destroy', $m->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu kargo metodunu silmek istediğinizden emin misiniz?')">
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
                    <td colspan="7" class="text-center">
                        <div style="padding:40px">
                            <span class="material-icons" style="font-size:48px;color:#bdbdbd">local_shipping</span>
                            <p class="text-muted mt-2">Henüz kargo metodu eklenmemiş.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="material-pagination">{{ $methods->links() }}</div>
@endsection
