@extends('admin.layout')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h1 class="h4 mb-3">Rol Düzenle: {{ $role->name }}</h1>
            
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Rol Adı</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $role->name) }}" required>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Açıklama</label>
                    <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $role->description) }}</textarea>
                </div>
                
             

                <div class="mb-3">
                    <label class="form-label">Route İzinleri</label>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Bilgi:</strong> Route izinleri, bu rolün hangi sayfalara erişebileceğini ve hangi işlemleri yapabileceğini belirler.
                    </div>
                    
                    @foreach($routeGroups as $group)
                        <div class="card mb-3">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">{{ ucfirst($group) }} Route'ları</h6>
                                    <div class="form-check">
                                        <input class="form-check-input select-all-group" type="checkbox" data-group="{{ $group }}" id="select_all_{{ $group }}">
                                        <label class="form-check-label" for="select_all_{{ $group }}">
                                            Tümünü Seç
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
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
                                                        <span class="badge bg-{{ $route->method == 'GET' ? 'success' : ($route->method == 'POST' ? 'primary' : ($route->method == 'PUT' ? 'warning' : ($route->method == 'DELETE' ? 'danger' : 'info'))) }}">
                                                            {{ $route->method }}
                                                        </span>
                                                        @if($routeImportance === 'critical')
                                                            <span class="badge bg-danger ms-1">Kritik</span>
                                                        @elseif($routeImportance === 'important')
                                                            <span class="badge bg-warning ms-1">Önemli</span>
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
                
                <div class="mb-3">
                    <div class="alert alert-info">
                        <strong>Bu rolü kullanan kullanıcılar:</strong> {{ $role->users()->count() }} kullanıcı
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Güncelle
                    </button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Geri
                    </a>
                </div>
            </form>
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