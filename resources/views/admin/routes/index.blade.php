@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Route Yönetimi</h1>
    <div>
        <a href="{{ route('admin.routes.import') }}" class="btn btn-success">
            <i class="fas fa-download"></i> Route'ları Import Et
        </a>
        <a href="{{ route('admin.routes.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yeni Route Ekle
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">Route Listesi</h5>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    <div class="btn-group" role="group">
                        <a href="{{ route('admin.routes.index') }}" class="btn btn-outline-secondary {{ !request('group') ? 'active' : '' }}">Tümü</a>
                        @foreach($routes->pluck('group')->unique()->filter() as $group)
                            <a href="{{ route('admin.routes.by-group', $group) }}" class="btn btn-outline-secondary {{ request('group') == $group ? 'active' : '' }}">{{ $group }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Route Adı</th>
                        <th>URI</th>
                        <th>Method</th>
                        <th>Grup</th>
                        <th>Açıklama</th>
                        <th>Durum</th>
                        <th>Roller</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($routes as $route)
                        <tr>
                            <td>{{ $route->id }}</td>
                            <td>
                                <code>{{ $route->name }}</code>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $route->method }}</span>
                                <code>{{ $route->uri }}</code>
                            </td>
                            <td>
                                <span class="badge bg-{{ $route->method == 'GET' ? 'success' : ($route->method == 'POST' ? 'primary' : ($route->method == 'PUT' ? 'warning' : ($route->method == 'DELETE' ? 'danger' : 'info'))) }}">
                                    {{ $route->method }}
                                </span>
                            </td>
                            <td>
                                @if($route->group)
                                    <span class="badge bg-info">{{ $route->group }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $route->description ?: '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $route->is_active ? 'success' : 'danger' }}">
                                    {{ $route->is_active ? 'Aktif' : 'Pasif' }}
                                </span>
                            </td>
                            <td>
                                @if($route->roles->count() > 0)
                                    @foreach($route->roles->take(3) as $role)
                                        <span class="badge bg-primary">{{ $role->name }}</span>
                                    @endforeach
                                    @if($route->roles->count() > 3)
                                        <span class="badge bg-secondary">+{{ $route->roles->count() - 3 }}</span>
                                    @endif
                                @else
                                    <span class="text-muted">Rol yok</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.routes.show', $route) }}" class="btn btn-sm btn-outline-info" title="Görüntüle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.routes.edit', $route) }}" class="btn btn-sm btn-outline-primary" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.routes.toggle-status', $route) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-outline-{{ $route->is_active ? 'warning' : 'success' }}" title="{{ $route->is_active ? 'Pasif Yap' : 'Aktif Yap' }}">
                                            <i class="fas fa-{{ $route->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.routes.destroy', $route) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu route\'u silmek istediğinize emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Sil">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">Henüz route bulunmuyor.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                Toplam {{ $routes->total() }} route'dan {{ $routes->firstItem() }}-{{ $routes->lastItem() }} arası gösteriliyor
            </div>
            @if($routes->hasPages())
                <nav aria-label="Route sayfaları">
                    {{ $routes->links() }}
                </nav>
            @endif
        </div>
    </div>
</div>
@endsection 