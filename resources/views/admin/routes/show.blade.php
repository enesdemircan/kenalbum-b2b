@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Route Detayları: {{ $route->name }}</h1>
    <div>
        <a href="{{ route('admin.routes.edit', $route) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Düzenle
        </a>
        <a href="{{ route('admin.routes.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Geri Dön
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Route Bilgileri</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Route Adı:</th>
                                <td><code>{{ $route->name }}</code></td>
                            </tr>
                            <tr>
                                <th>URI:</th>
                                <td>
                                    <span class="badge bg-secondary">{{ $route->method }}</span>
                                    <code>{{ $route->uri }}</code>
                                </td>
                            </tr>
                            <tr>
                                <th>HTTP Method:</th>
                                <td>
                                    <span class="badge bg-{{ $route->method == 'GET' ? 'success' : ($route->method == 'POST' ? 'primary' : ($route->method == 'PUT' ? 'warning' : ($route->method == 'DELETE' ? 'danger' : 'info'))) }}">
                                        {{ $route->method }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Grup:</th>
                                <td>
                                    @if($route->group)
                                        <span class="badge bg-info">{{ $route->group }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="150">Durum:</th>
                                <td>
                                    <span class="badge bg-{{ $route->is_active ? 'success' : 'danger' }}">
                                        {{ $route->is_active ? 'Aktif' : 'Pasif' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Açıklama:</th>
                                <td>{{ $route->description ?: '-' }}</td>
                            </tr>
                            <tr>
                                <th>Oluşturulma:</th>
                                <td>{{ $route->created_at->format('d.m.Y H:i') }}</td>
                            </tr>
                            <tr>
                                <th>Güncellenme:</th>
                                <td>{{ $route->updated_at->format('d.m.Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">İstatistikler</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <h4 class="text-primary">{{ $route->roles->count() }}</h4>
                            <small class="text-muted">Rol</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-3">
                            <h4 class="text-success">{{ $route->roles->where('pivot.can_access', true)->count() }}</h4>
                            <small class="text-muted">Erişim İzni</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Bu Route'a Sahip Roller</h5>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#assignRoleModal">
            <i class="fas fa-plus"></i> Rol Ata
        </button>
    </div>
    <div class="card-body">
        @if($route->roles->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Rol Adı</th>
                            <th>Erişim</th>
                            <th>Oluşturma</th>
                            <th>Okuma</th>
                            <th>Güncelleme</th>
                            <th>Silme</th>
                            <th>İşlemler</th>
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
                                    <span class="badge bg-{{ $role->pivot->can_access ? 'success' : 'danger' }}">
                                        {{ $role->pivot->can_access ? 'Evet' : 'Hayır' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $role->pivot->can_create ? 'success' : 'secondary' }}">
                                        {{ $role->pivot->can_create ? 'Evet' : 'Hayır' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $role->pivot->can_read ? 'success' : 'secondary' }}">
                                        {{ $role->pivot->can_read ? 'Evet' : 'Hayır' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $role->pivot->can_update ? 'success' : 'secondary' }}">
                                        {{ $role->pivot->can_update ? 'Evet' : 'Hayır' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $role->pivot->can_delete ? 'success' : 'secondary' }}">
                                        {{ $role->pivot->can_delete ? 'Evet' : 'Hayır' }}
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            onclick="editRolePermissions({{ $role->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('admin.routes.remove-role', [$route, $role]) }}" 
                                          method="POST" class="d-inline" 
                                          onsubmit="return confirm('Bu rolü kaldırmak istediğinize emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h6 class="text-muted">Henüz rol atanmamış</h6>
                <p class="text-muted">Bu route'a rol atamak için yukarıdaki "Rol Ata" butonunu kullanın.</p>
            </div>
        @endif
    </div>
</div>

<!-- Rol Atama Modal -->
<div class="modal fade" id="assignRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.routes.assign-role', $route) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Rol Ata</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="role_id" class="form-label">Rol Seçin</label>
                        <select name="role_id" id="role_id" class="form-select" required>
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Rol Ata</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editRolePermissions(roleId) {
    // Bu fonksiyon rol izinlerini düzenlemek için kullanılacak
    alert('Rol izinlerini düzenleme özelliği yakında eklenecek!');
}
</script>
@endsection 