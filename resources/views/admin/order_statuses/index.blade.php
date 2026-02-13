@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Sipariş Durumları</h1>
    <a href="{{ route('admin.order-statuses.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Yeni Durum Ekle
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Durum Adı</th>
            <th>Roller</th>
            <th>Oluşturulma Tarihi</th>
            <th style="width: 190px;"></th>
        </tr>
    </thead>
    <tbody>
        @forelse($orderStatuses as $status)
            <tr>
                <td>
                    <span class="badge bg-primary">{{ $status->title }}</span>
                </td>
                <td>
                    @if($status->roles->count() > 0)
                        @foreach($status->roles as $role)
                            <span class="badge bg-info me-1">{{ $role->name }}</span>
                        @endforeach
                    @else
                        <span class="text-muted">Rol atanmamış</span>
                    @endif
                </td>
                <td>{{ $status->created_at->format('d.m.Y H:i') }}</td>
                <td>
                    <a href="{{ route('admin.order-statuses.edit', $status->id) }}" class="btn btn-sm btn-warning" title="Düzenle">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <form action="{{ route('admin.order-statuses.destroy', $status->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bu durumu silmek istediğinizden emin misiniz?')" title="Sil">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="text-center">Henüz sipariş durumu eklenmemiş.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- Sayfalama -->
<div class="d-flex justify-content-center mt-4">
    {{ $orderStatuses->links() }}
</div>

@endsection 