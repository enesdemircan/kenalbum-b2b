@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Kullanıcı Yönetimi</h1>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Yeni Kullanıcı Ekle
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<!-- Filtre Formu -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Filtrele</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
            <div class="col">
                <label for="name" class="form-label">Ad</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $filters['name'] ?? '' }}" placeholder="Ad ara...">
            </div>
            <div class="col">
                <label for="email" class="form-label">E-posta</label>
                <input type="text" class="form-control" id="email" name="email" value="{{ $filters['email'] ?? '' }}" placeholder="E-posta ara...">
            </div>
            <div class="col">
                <label for="role_id" class="form-label">Rol</label>
                <select class="form-select" id="role_id" name="role_id">
                    <option value="">Tüm Roller</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ ($filters['role_id'] ?? '') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col">
                <label for="customer_id" class="form-label">Firma</label>
                <select class="form-select" id="customer_id" name="customer_id">
                    <option value="">Tüm Firmalar</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ ($filters['customer_id'] ?? '') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->unvan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col">
                <label for="status" class="form-label">Durum</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Tüm Durumlar</option>
                    <option value="0" {{ ($filters['status'] ?? '') == '0' ? 'selected' : '' }}>Onay Bekliyor</option>
                    <option value="1" {{ ($filters['status'] ?? '') == '1' ? 'selected' : '' }}>Onaylandı</option>
                </select>
            </div>
            <div class="col-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search"></i> Filtrele
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Temizle
                </a>
            </div>
        </form>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Ad</th>
                <th>E-posta</th>
                <th>Roller</th>
                <th>Firma</th>
                <th>Durum</th>
                <th style="width: 250px;">İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @foreach($user->roles as $role)
                            <span class="badge bg-primary">{{ $role->name }}</span>
                        @endforeach
                    </td>
                    <td>
                        @if($user->customer)
                            <span class="badge bg-success">{{ $user->customer->unvan }}</span>
                        @else
                            <span class="badge bg-secondary">Atanmamış</span>
                        @endif
                    </td>
                    <td>
                        @if($user->status == 1)
                            <span class="badge bg-success">Onaylandı</span>
                        @else
                            <span class="badge bg-warning">Onay Bekliyor</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-success" title="Düzenle">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#roleModal{{ $user->id }}" title="Roller">
                            <i class="bi bi-person-badge"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#customerModal{{ $user->id }}" title="Firma Ata">
                            <i class="bi bi-building"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#statusModal{{ $user->id }}" title="Durum Güncelle">
                            <i class="bi bi-toggle-on"></i>
                        </button>
                    </td>
                </tr>
                
                <!-- Role Modal -->
                <div class="modal fade" id="roleModal{{ $user->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ $user->name }} - Rolleri Düzenle</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('admin.users.update-role', $user) }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    @foreach($roles as $role)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                                id="role{{ $user->id }}_{{ $role->id }}"
                                                {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="role{{ $user->id }}_{{ $role->id }}">
                                                {{ $role->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                    <button type="submit" class="btn btn-primary">Kaydet</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Customer Modal -->
                <div class="modal fade" id="customerModal{{ $user->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ $user->name }} - Firma Ata</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('admin.users.update-customer', $user) }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="customer_id" class="form-label">Firma Seçin</label>
                                        <select name="customer_id" id="customer_id" class="form-select">
                                            <option value="">Firma Atanmamış</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" {{ $user->customer_id == $customer->id ? 'selected' : '' }}>
                                                    {{ $customer->unvan }} ({{ $customer->firma_id }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                    <button type="submit" class="btn btn-primary">Kaydet</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Status Modal -->
                <div class="modal fade" id="statusModal{{ $user->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">{{ $user->name }} - Durum Güncelle</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('admin.users.update-status', $user) }}" method="POST">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Kullanıcı Durumu</label>
                                        <select name="status" id="status" class="form-select">
                                            <option value="0" {{ $user->status == 0 ? 'selected' : '' }}>Onay Bekliyor</option>
                                            <option value="1" {{ $user->status == 1 ? 'selected' : '' }}>Onaylandı</option>
                                        </select>
                                    </div>
                                    <div class="alert alert-info">
                                        <strong>Bilgi:</strong> Onay bekliyor durumundaki kullanıcılar sisteme giriş yapamaz ve onay bekliyor sayfasına yönlendirilir.
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                                    <button type="submit" class="btn btn-primary">Kaydet</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Sayfalama -->
<div class="d-flex justify-content-center mt-4">
    {{ $users->appends(request()->query())->links() }}
</div>

@endsection 