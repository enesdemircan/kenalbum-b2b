@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1 class="page-title">Route Yönetimi</h1>
        <p class="page-subtitle">Sistem route'larını görüntüleyin ve yönetin</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.routes.import') }}" class="btn-material btn-material-success">
            <span class="material-icons">download</span>
            Route'ları Import Et
        </a>
        <a href="{{ route('admin.routes.create') }}" class="btn-material btn-material-primary">
            <span class="material-icons">add</span>
            Yeni Route Ekle
        </a>
    </div>
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

<div class="material-card-elevated">
    <div class="material-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 style="margin:0"><span class="material-icons" style="vertical-align:middle;margin-right:8px">route</span>Route Listesi</h5>
        <div class="d-flex gap-1 flex-wrap">
            <a href="{{ route('admin.routes.index') }}" class="btn-material {{ !request('group') ? 'btn-material-primary' : 'btn-material-secondary' }}" style="padding:6px 14px;font-size:13px">
                Tümü
            </a>
            @foreach($routes->pluck('group')->unique()->filter() as $group)
                <a href="{{ route('admin.routes.by-group', $group) }}" class="btn-material {{ request('group') == $group ? 'btn-material-primary' : 'btn-material-secondary' }}" style="padding:6px 14px;font-size:13px">
                    {{ $group }}
                </a>
            @endforeach
        </div>
    </div>
    <div class="material-card-body">
        <div class="material-table-wrapper">
            <table class="material-table">
                <thead>
                    <tr>
                        <th>Route Adı</th>
                        <th>URI</th>
                        <th>Method</th>
                        <th>Grup</th>
                        <th>Durum</th>
                        <th>Roller</th>
                        <th style="width: 180px">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($routes as $route)
                        <tr>
                            <td><code style="background:#f5f5f5;padding:2px 6px;border-radius:4px;font-size:12px">{{ $route->name }}</code></td>
                            <td>
                                <span class="material-badge material-badge-{{ $route->method == 'GET' ? 'success' : ($route->method == 'POST' ? 'primary' : ($route->method == 'PUT' || $route->method == 'PATCH' ? 'warning' : ($route->method == 'DELETE' ? 'danger' : 'info'))) }}" style="font-size:11px">{{ $route->method }}</span>
                                <code style="font-size:11px">{{ $route->uri }}</code>
                            </td>
                            <td>
                                <span class="material-badge material-badge-{{ $route->method == 'GET' ? 'success' : ($route->method == 'POST' ? 'primary' : ($route->method == 'PUT' || $route->method == 'PATCH' ? 'warning' : ($route->method == 'DELETE' ? 'danger' : 'info'))) }}">
                                    {{ $route->method }}
                                </span>
                            </td>
                            <td>
                                @if($route->group)
                                    <span class="material-badge material-badge-info">{{ $route->group }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="material-badge material-badge-{{ $route->is_active ? 'success' : 'danger' }}">
                                    {{ $route->is_active ? 'Aktif' : 'Pasif' }}
                                </span>
                            </td>
                            <td>
                                @if($route->roles->count() > 0)
                                    @foreach($route->roles->take(3) as $role)
                                        <span class="material-badge material-badge-primary">{{ $role->name }}</span>
                                    @endforeach
                                    @if($route->roles->count() > 3)
                                        <span class="material-badge material-badge-secondary">+{{ $route->roles->count() - 3 }}</span>
                                    @endif
                                @else
                                    <span class="text-muted">Rol yok</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-end">
                                    <button class="btn-material-icon btn-material-icon-info" title="Görüntüle" onclick="window.location.href='{{ route('admin.routes.show', $route) }}'">
                                        <span class="material-icons">visibility</span>
                                    </button>
                                    <button class="btn-material-icon btn-material-icon-warning" title="Düzenle" onclick="window.location.href='{{ route('admin.routes.edit', $route) }}'">
                                        <span class="material-icons">edit</span>
                                    </button>
                                    <form action="{{ route('admin.routes.toggle-status', $route) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn-material-icon btn-material-icon-{{ $route->is_active ? 'warning' : 'success' }}" title="{{ $route->is_active ? 'Pasif Yap' : 'Aktif Yap' }}">
                                            <span class="material-icons">{{ $route->is_active ? 'pause' : 'play_arrow' }}</span>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.routes.destroy', $route) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu route\'u silmek istediğinize emin misiniz?')">
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
                                <div style="padding: 40px">
                                    <span class="material-icons" style="font-size: 48px; color: #bdbdbd">route</span>
                                    <p class="text-muted mt-2">Henüz route bulunmuyor.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
            <div class="text-muted small">
                Toplam {{ $routes->total() }} route'dan {{ $routes->firstItem() ?? 0 }}-{{ $routes->lastItem() ?? 0 }} arası gösteriliyor
            </div>
            @if($routes->hasPages())
                <div class="material-pagination">
                    {{ $routes->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
