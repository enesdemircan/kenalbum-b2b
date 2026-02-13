@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Kullanıcı Yönetimi</h1>
        <p class="page-subtitle">Tüm kullanıcıları görüntüleyin ve yönetin</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn-material btn-material-primary">
        <span class="material-icons">add</span>
        Yeni Kullanıcı Ekle
    </a>
</div>

@if(session('success'))
    <div class="material-alert material-alert-success">
        <span class="material-icons">check_circle</span>
        <span>{{ session('success') }}</span>
    </div>
@endif

<!-- Filtre Accordion -->
<div class="filter-accordion material-card">
    <div class="filter-header" id="filterHeader" onclick="toggleFilterAccordion()">
        <span class="material-icons">filter_list</span>
        <span>Filtrele</span>
        <span class="material-icons expand-icon">expand_more</span>
    </div>
    <div class="filter-body" id="filterBody">
        <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="name" class="form-label">Ad</label>
                <input type="text" class="form-control form-control-material" id="name" name="name" value="{{ $filters['name'] ?? '' }}" placeholder="Ad ara...">
            </div>
            <div class="col-md-3">
                <label for="email" class="form-label">E-posta</label>
                <input type="text" class="form-control form-control-material" id="email" name="email" value="{{ $filters['email'] ?? '' }}" placeholder="E-posta ara...">
            </div>
            <div class="col-md-2">
                <label for="role_id" class="form-label">Rol</label>
                <select class="form-select form-control-material" id="role_id" name="role_id">
                    <option value="">Tüm Roller</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ ($filters['role_id'] ?? '') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="customer_id" class="form-label">Firma</label>
                <select class="form-select form-control-material" id="customer_id" name="customer_id">
                    <option value="">Tüm Firmalar</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ ($filters['customer_id'] ?? '') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->unvan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Durum</label>
                <select class="form-select form-control-material" id="status" name="status">
                    <option value="">Tüm Durumlar</option>
                    <option value="0" {{ ($filters['status'] ?? '') == '0' ? 'selected' : '' }}>Onay Bekliyor</option>
                    <option value="1" {{ ($filters['status'] ?? '') == '1' ? 'selected' : '' }}>Onaylandı</option>
                </select>
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <button type="submit" class="btn-material btn-material-primary">
                    <span class="material-icons">search</span>
                    Filtrele
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn-material btn-material-secondary">
                    <span class="material-icons">clear</span>
                    Temizle
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Kullanıcılar Tablosu -->
<div class="material-table-wrapper">
    <table class="material-table">
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
                            <span class="material-badge material-badge-primary">{{ $role->name }}</span>
                        @endforeach
                    </td>
                    <td>
                        @if($user->customer)
                            <span class="material-badge material-badge-success">{{ $user->customer->unvan }}</span>
                        @else
                            <span class="material-badge material-badge-secondary">Atanmamış</span>
                        @endif
                    </td>
                    <td>
                        @if($user->status == 1)
                            <span class="material-badge material-badge-success">Onaylandı</span>
                        @else
                            <span class="material-badge material-badge-warning">Onay Bekliyor</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-1 justify-content-end">
                            <button class="btn-material-icon btn-material-icon-success" title="Düzenle" onclick="window.location.href='{{ route('admin.users.edit', $user) }}'">
                                <span class="material-icons">edit</span>
                            </button>
                            <button type="button" class="btn-material-icon btn-material-icon-primary" data-bs-toggle="modal" data-bs-target="#roleModal{{ $user->id }}" title="Roller">
                                <span class="material-icons">badge</span>
                            </button>
                            <button type="button" class="btn-material-icon btn-material-icon-info" data-bs-toggle="modal" data-bs-target="#customerModal{{ $user->id }}" title="Firma">
                                <span class="material-icons">business</span>
                            </button>
                            <button type="button" class="btn-material-icon btn-material-icon-warning" data-bs-toggle="modal" data-bs-target="#statusModal{{ $user->id }}" title="Durum">
                                <span class="material-icons">toggle_on</span>
                            </button>
                        </div>
                    </td>
                </tr>
                
                <!-- Role Modal -->
                <div class="modal fade" id="roleModal{{ $user->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-material">
                        <div class="modal-content material-modal">
                            <div class="modal-header material-modal-header">
                                <h5 class="modal-title">{{ $user->name }} - Rolleri Düzenle</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('admin.users.update-role', $user) }}" method="POST">
                                @csrf
                                <div class="modal-body material-modal-body">
                                    @foreach($roles as $role)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                                id="role{{ $user->id }}_{{ $role->id }}"
                                                {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="role{{ $user->id }}_{{ $role->id }}">
                                                {{ $role->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="modal-footer material-modal-footer">
                                    <button type="button" class="btn-material btn-material-secondary" data-bs-dismiss="modal">İptal</button>
                                    <button type="submit" class="btn-material btn-material-primary">Kaydet</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Customer Modal -->
                <div class="modal fade" id="customerModal{{ $user->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-material">
                        <div class="modal-content material-modal">
                            <div class="modal-header material-modal-header">
                                <h5 class="modal-title">{{ $user->name }} - Firma Ata</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('admin.users.update-customer', $user) }}" method="POST">
                                @csrf
                                <div class="modal-body material-modal-body">
                                    <div class="mb-3">
                                        <label for="customer_id" class="form-label">Firma Seçin</label>
                                        <select name="customer_id" id="customer_id" class="form-select form-control-material">
                                            <option value="">Firma Atanmamış</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" {{ $user->customer_id == $customer->id ? 'selected' : '' }}>
                                                    {{ $customer->unvan }} ({{ $customer->firma_id }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer material-modal-footer">
                                    <button type="button" class="btn-material btn-material-secondary" data-bs-dismiss="modal">İptal</button>
                                    <button type="submit" class="btn-material btn-material-primary">Kaydet</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Status Modal -->
                <div class="modal fade" id="statusModal{{ $user->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-material">
                        <div class="modal-content material-modal">
                            <div class="modal-header material-modal-header">
                                <h5 class="modal-title">{{ $user->name }} - Durum Güncelle</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('admin.users.update-status', $user) }}" method="POST">
                                @csrf
                                <div class="modal-body material-modal-body">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Kullanıcı Durumu</label>
                                        <select name="status" id="status" class="form-select form-control-material">
                                            <option value="0" {{ $user->status == 0 ? 'selected' : '' }}>Onay Bekliyor</option>
                                            <option value="1" {{ $user->status == 1 ? 'selected' : '' }}>Onaylandı</option>
                                        </select>
                                    </div>
                                    <div class="material-alert material-alert-info">
                                        <span class="material-icons">info</span>
                                        <span><strong>Bilgi:</strong> Onay bekliyor durumundaki kullanıcılar sisteme giriş yapamaz ve onay bekliyor sayfasına yönlendirilir.</span>
                                    </div>
                                </div>
                                <div class="modal-footer material-modal-footer">
                                    <button type="button" class="btn-material btn-material-secondary" data-bs-dismiss="modal">İptal</button>
                                    <button type="submit" class="btn-material btn-material-primary">Kaydet</button>
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
<div class="material-pagination">
    {{ $users->appends(request()->query())->links() }}
</div>

<script>
    // Filter Accordion Toggle
    function toggleFilterAccordion() {
        const header = document.getElementById('filterHeader');
        const body = document.getElementById('filterBody');
        const isOpen = body.classList.contains('open');
        
        if (isOpen) {
            body.classList.remove('open');
            header.classList.remove('active');
            localStorage.setItem('usersFilterOpen', 'false');
        } else {
            body.classList.add('open');
            header.classList.add('active');
            localStorage.setItem('usersFilterOpen', 'true');
        }
    }
    
    // Remember filter state
    document.addEventListener('DOMContentLoaded', function() {
        const filterOpen = localStorage.getItem('usersFilterOpen');
        if (filterOpen === 'true') {
            document.getElementById('filterBody').classList.add('open');
            document.getElementById('filterHeader').classList.add('active');
        }
    });
</script>

@endsection
