@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Sipariş Durumları</h1>
        <p class="page-subtitle">Sipariş durumlarını görüntüleyin ve yönetin</p>
    </div>
    <a href="{{ route('admin.order-statuses.create') }}" class="btn-material btn-material-primary">
        <span class="material-icons">add</span>
        Yeni Durum Ekle
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

<!-- Sipariş Durumları Tablosu -->
<div class="material-table-wrapper">
    <table class="material-table">
        <thead>
            <tr>
                <th>Durum Adı</th>
                <th>Roller</th>
                <th>Oluşturulma Tarihi</th>
                <th style="width: 150px">İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orderStatuses as $status)
                <tr>
                    <td>
                        <span class="material-badge material-badge-primary">{{ $status->title }}</span>
                    </td>
                    <td>
                        @if($status->roles->count() > 0)
                            @foreach($status->roles as $role)
                                <span class="material-badge material-badge-info">{{ $role->name }}</span>
                            @endforeach
                        @else
                            <span class="text-muted">Rol atanmamış</span>
                        @endif
                    </td>
                    <td>{{ $status->created_at->format('d.m.Y H:i') }}</td>
                    <td>
                        <div class="d-flex gap-1 justify-content-end">
                            <button class="btn-material-icon btn-material-icon-warning" title="Düzenle" onclick="window.location.href='{{ route('admin.order-statuses.edit', $status->id) }}'">
                                <span class="material-icons">edit</span>
                            </button>
                            <form action="{{ route('admin.order-statuses.destroy', $status->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu durumu silmek istediğinizden emin misiniz?')">
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
                    <td colspan="4" class="text-center">
                        <div style="padding: 40px">
                            <span class="material-icons" style="font-size: 48px; color: #bdbdbd">inventory_2</span>
                            <p class="text-muted mt-2">Henüz sipariş durumu eklenmemiş.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Sayfalama -->
<div class="material-pagination">
    {{ $orderStatuses->links() }}
</div>

@endsection
