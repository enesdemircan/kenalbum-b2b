@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Rol Yönetimi</h1>
        <p class="page-subtitle">Kullanıcı rollerini ve izinlerini yönetin</p>
    </div>
    <a href="{{ route('admin.roles.create') }}" class="btn-material btn-material-primary">
        <span class="material-icons">add</span>
        Yeni Rol Ekle
    </a>
</div>

@if(session('success'))
    <div class="material-alert material-alert-success mb-3">
        <span class="material-icons">check_circle</span>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="material-alert material-alert-danger mb-3">
        <span class="material-icons">error</span>
        <span>{{ session('error') }}</span>
    </div>
@endif

<div class="material-table-wrapper">
    <table class="material-table">
        <thead>
            <tr>
                <th>Rol Adı</th>
                <th>Açıklama</th>
                <th>İzinler</th>
                <th>Kullanıcı Sayısı</th>
                <th style="width: 140px">İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
                <tr>
                    <td><strong>{{ $role->name }}</strong></td>
                    <td>{{ $role->description ?? 'Açıklama yok' }}</td>
                    <td>
                        @if($role->permissions)
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($role->permissions as $permission)
                                    <span class="material-badge material-badge-info">{{ $permission }}</span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted">İzin yok</span>
                        @endif
                    </td>
                    <td>
                        <span class="material-badge material-badge-secondary">{{ $role->users()->count() }} kullanıcı</span>
                    </td>
                    <td>
                        <div class="d-flex gap-1 justify-content-end">
                            <button class="btn-material-icon btn-material-icon-warning" title="Düzenle" onclick="window.location.href='{{ route('admin.roles.edit', $role->id) }}'">
                                <span class="material-icons">edit</span>
                            </button>
                            @if($role->users()->count() == 0)
                                <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu rolü silmek istediğinizden emin misiniz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-material-icon btn-material-icon-danger" title="Sil">
                                        <span class="material-icons">delete</span>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="material-pagination">
    {{ $roles->links() }}
</div>
@endsection
