@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Route Detayları</h1>
        <p class="page-subtitle">{{ $route->name }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.routes.index') }}" class="btn-material btn-material-secondary">
            <span class="material-icons">arrow_back</span>
            Geri Dön
        </a>
        <a href="{{ route('admin.routes.edit', $route) }}" class="btn-material btn-material-primary">
            <span class="material-icons">edit</span>
            Düzenle
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="material-card-elevated">
            <div class="material-card-header">
                <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">info</span>Route Bilgileri</h5>
            </div>
            <div class="material-card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="material-info-grid" style="display:grid;gap:12px">
                            <div><strong class="text-muted">Route Adı:</strong><br><code style="background:#f5f5f5;padding:4px 8px;border-radius:4px">{{ $route->name }}</code></div>
                            <div><strong class="text-muted">URI:</strong><br>
                                <span class="material-badge material-badge-secondary">{{ $route->method }}</span>
                                <code>{{ $route->uri }}</code>
                            </div>
                            <div><strong class="text-muted">HTTP Method:</strong><br>
                                <span class="material-badge material-badge-{{ $route->method == 'GET' ? 'success' : ($route->method == 'POST' ? 'primary' : ($route->method == 'PUT' || $route->method == 'PATCH' ? 'warning' : ($route->method == 'DELETE' ? 'danger' : 'info'))) }}">
                                    {{ $route->method }}
                                </span>
                            </div>
                            <div><strong class="text-muted">Grup:</strong><br>
                                @if($route->group)
                                    <span class="material-badge material-badge-info">{{ $route->group }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="material-info-grid" style="display:grid;gap:12px">
                            <div><strong class="text-muted">Durum:</strong><br>
                                <span class="material-badge material-badge-{{ $route->is_active ? 'success' : 'danger' }}">
                                    {{ $route->is_active ? 'Aktif' : 'Pasif' }}
                                </span>
                            </div>
                            <div><strong class="text-muted">Açıklama:</strong><br>{{ $route->description ?: '-' }}</div>
                            <div><strong class="text-muted">Oluşturulma:</strong><br>{{ $route->created_at->format('d.m.Y H:i') }}</div>
                            <div><strong class="text-muted">Güncellenme:</strong><br>{{ $route->updated_at->format('d.m.Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="material-card-elevated">
            <div class="material-card-header">
                <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">analytics</span>İstatistikler</h5>
            </div>
            <div class="material-card-body">
                <div class="row g-3 text-center">
                    <div class="col-6">
                        <div class="material-card-outlined" style="padding:20px">
                            <div style="font-size:28px;font-weight:500;color:var(--md-primary)">{{ $route->roles->count() }}</div>
                            <small class="text-muted">Rol</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="material-card-outlined" style="padding:20px">
                            <div style="font-size:28px;font-weight:500;color:var(--md-success)">{{ $route->roles->where('pivot.can_access', true)->count() }}</div>
                            <small class="text-muted">Erişim İzni</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="material-card-elevated mt-4">
    <div class="material-card-header d-flex justify-content-between align-items-center">
        <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">group</span>Bu Route'a Sahip Roller</h5>
        <button type="button" class="btn-material btn-material-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assignRoleModal">
            <span class="material-icons" style="font-size:18px">add</span>
            Rol Ata
        </button>
    </div>
    <div class="material-card-body">
        @if($route->roles->count() > 0)
            <div class="material-table-wrapper">
                <table class="material-table">
                    <thead>
                        <tr>
                            <th>Rol Adı</th>
                            <th>Erişim</th>
                            <th>Oluşturma</th>
                            <th>Okuma</th>
                            <th>Güncelleme</th>
                            <th>Silme</th>
                            <th style="width: 120px">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($route->roles as $role)
                            <tr>
                                <td>
                                    <strong>{{ $role->name }}</strong>
                                    @if($role->description)
                                        <br><small class="text-muted">{{ $role->description }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="material-badge material-badge-{{ $role->pivot->can_access ? 'success' : 'danger' }}">
                                        {{ $role->pivot->can_access ? 'Evet' : 'Hayır' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="material-badge material-badge-{{ $role->pivot->can_create ? 'success' : 'secondary' }}">
                                        {{ $role->pivot->can_create ? 'Evet' : 'Hayır' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="material-badge material-badge-{{ $role->pivot->can_read ? 'success' : 'secondary' }}">
                                        {{ $role->pivot->can_read ? 'Evet' : 'Hayır' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="material-badge material-badge-{{ $role->pivot->can_update ? 'success' : 'secondary' }}">
                                        {{ $role->pivot->can_update ? 'Evet' : 'Hayır' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="material-badge material-badge-{{ $role->pivot->can_delete ? 'success' : 'secondary' }}">
                                        {{ $role->pivot->can_delete ? 'Evet' : 'Hayır' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn-material-icon btn-material-icon-warning" title="Düzenle" onclick="editRolePermissions({{ $role->id }})">
                                            <span class="material-icons">edit</span>
                                        </button>
                                        <form action="{{ route('admin.routes.remove-role', [$route, $role]) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu rolü kaldırmak istediğinize emin misiniz?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-material-icon btn-material-icon-danger" title="Kaldır">
                                                <span class="material-icons">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <span class="material-icons" style="font-size: 48px; color: #bdbdbd">group</span>
                <h6 class="text-muted mt-2">Henüz rol atanmamış</h6>
                <p class="text-muted small">Bu route'a rol atamak için yukarıdaki "Rol Ata" butonunu kullanın.</p>
            </div>
        @endif
    </div>
</div>

<!-- Rol Atama Modal -->
<div class="modal fade" id="assignRoleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-material">
        <div class="modal-content material-modal">
            <form action="{{ route('admin.routes.assign-role', $route) }}" method="POST">
                @csrf
                <div class="material-modal-header">
                    <h5 class="modal-title">Rol Ata</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="material-modal-body">
                    <div class="mb-3">
                        <label for="role_id" class="form-label">Rol Seçin</label>
                        <select name="role_id" id="role_id" class="form-select form-control-material" required>
                            <option value="">Rol seçin...</option>
                            @foreach(\App\Models\Role::all() as $role)
                                @if(!$route->roles->contains($role->id))
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">İzinler</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[can_access]" value="1" checked>
                            <label class="form-check-label">Erişim İzni</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[can_create]" value="1">
                            <label class="form-check-label">Oluşturma İzni</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[can_read]" value="1">
                            <label class="form-check-label">Okuma İzni</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[can_update]" value="1">
                            <label class="form-check-label">Güncelleme İzni</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[can_delete]" value="1">
                            <label class="form-check-label">Silme İzni</label>
                        </div>
                    </div>
                </div>
                <div class="material-modal-footer">
                    <button type="button" class="btn-material btn-material-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn-material btn-material-primary">Rol Ata</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editRolePermissions(roleId) {
    alert('Rol izinlerini düzenleme özelliği yakında eklenecek!');
}
</script>
@endsection
