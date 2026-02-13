@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Rol Yönetimi</h1>
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Yeni Rol Ekle
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
            <th>ID</th>
            <th>Rol Adı</th>
            <th>Açıklama</th>
            <th>İzinler</th>
            <th>Kullanıcı Sayısı</th>
            <th style="width: 190px;"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($roles as $role)
            <tr>
                <td>{{ $role->id }}</td>
                <td>
                    <strong>{{ $role->name }}</strong>
                </td>
                <td>{{ $role->description ?? 'Açıklama yok' }}</td>
                <td>
                    @if($role->permissions)
                        @foreach($role->permissions as $permission)
                            <span class="badge bg-info me-1">{{ $permission }}</span>
                        @endforeach
                    @else
                        <span class="text-muted">İzin yok</span>
                    @endif
                </td>
                <td>
                    <span class="badge bg-secondary">{{ $role->users()->count() }} kullanıcı</span>
                </td>
                <td>
                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-warning" title="Düzenle">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    @if($role->users()->count() == 0)
                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bu rolü silmek istediğinizden emin misiniz?')" title="Sil">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Sayfalama -->
<div class="d-flex justify-content-center mt-4">
    {{ $roles->links() }}
</div>

@endsection 