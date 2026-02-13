@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Rol Düzenle</h1>
        <p class="page-subtitle">{{ $role->name }} - Rol ve route izinlerini güncelleyin</p>
    </div>
    <a href="{{ route('admin.roles.index') }}" class="btn-material btn-material-secondary">
        <span class="material-icons">arrow_back</span>
        Geri Dön
    </a>
</div>

@if($errors->any())
    <div class="material-alert material-alert-danger">
        <span class="material-icons">error</span>
        <div>
            <strong>Hatalar:</strong>
            <ul class="mb-0" style="margin-top: 8px">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="material-card-elevated">
    <div class="material-card-body">
        <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Rol Adı *</label>
                        <input type="text" name="name" id="name" class="form-control form-control-material" value="{{ old('name', $role->name) }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="description" class="form-label">Açıklama</label>
                        <textarea name="description" id="description" class="form-control form-control-material" rows="3">{{ old('description', $role->description) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Route İzinleri</label>
                <div class="material-alert material-alert-info mb-3">
                    <span class="material-icons">info</span>
                    <span><strong>Bilgi:</strong> Route izinleri, bu rolün hangi sayfalara erişebileceğini ve hangi işlemleri yapabileceğini belirler.</span>
                </div>
                
                @foreach($routeGroups as $group)
                    <div class="material-card-outlined mb-3">
                        <div class="material-card-header" style="background: #f8f9fa">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">{{ ucfirst($group) }} Route'ları</h6>
                                <div class="form-check">
                                    <input class="form-check-input select-all-group" type="checkbox" data-group="{{ $group }}" id="select_all_{{ $group }}">
                                    <label class="form-check-label" for="select_all_{{ $group }}">Tümünü Seç</label>
                                </div>
                            </div>
                        </div>
                        <div class="material-card-body">
                                @if(isset($routes[$group]))
                                    @foreach($routes[$group] as $route)

                                        <div class="row mb-2 route-item" data-group="{{ $group }}">
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input route-checkbox" type="checkbox" 
                                                           name="routes[]" 
                                                           value="{{ $route->id }}" 
                                                           id="route_{{ $route->id }}"
                                                           data-group="{{ $group }}"
                                                           {{ in_array($route->id, old('routes', $roleRoutes)) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="route_{{ $route->id }}">
                                                        <code>{{ $route->description }}</code>
                                                        <br>
                                                        <small class="text-muted">{{ $route->name }}</small>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                @php
                                                    $routeImportance = \App\Helpers\RoutePermissionHelper::getRouteImportance($route->name);
                                                @endphp
                                                
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <span class="material-badge material-badge-{{ $route->method == 'GET' ? 'success' : ($route->method == 'POST' ? 'primary' : ($route->method == 'PUT' ? 'warning' : ($route->method == 'DELETE' ? 'danger' : 'info'))) }}">
                                                            {{ $route->method }}
                                                        </span>
                                                        @if($routeImportance === 'critical')
                                                            <span class="material-badge material-badge-danger ms-1">Kritik</span>
                                                        @elseif($routeImportance === 'important')
                                                            <span class="material-badge material-badge-warning ms-1">Önemli</span>
                                                        @endif
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted mb-0">Bu grupta route bulunmuyor.</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="material-alert material-alert-info mb-4">
                    <span class="material-icons">people</span>
                    <span><strong>Bu rolü kullanan kullanıcılar:</strong> {{ $role->users()->count() }} kullanıcı</span>
                </div>
                
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.roles.index') }}" class="btn-material btn-material-secondary">
                        <span class="material-icons">close</span>
                        İptal
                    </a>
                    <button type="submit" class="btn-material btn-material-primary">
                        <span class="material-icons">save</span>
                        Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Grup bazlı tümünü seç/kaldır
    document.querySelectorAll('.select-all-group').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            const group = this.dataset.group;
            const isChecked = this.checked;
            
            // Bu gruba ait tüm route checkbox'larını seç/kaldır
            document.querySelectorAll(`.route-checkbox[data-group="${group}"]`).forEach(function(routeCheckbox) {
                routeCheckbox.checked = isChecked;
                
                // Route seçildiğinde/kaldırıldığında basit kontrol
                toggleRouteSelection(routeCheckbox);
            });
        });
    });
    
    // Route checkbox değişikliklerini dinle
    document.querySelectorAll('.route-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            toggleRouteSelection(this);
        });
    });
    
    // Route seçimi basit - sadece checkbox kontrolü
    function toggleRouteSelection(routeCheckbox) {
        // Route seçildiğinde otomatik olarak erişim izni verilir
        // Ekstra checkbox'lar yok, sadece route seçimi yeterli
    }
});
</script>
@endpush 