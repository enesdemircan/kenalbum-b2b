@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Firma Detayları</h1>
        <p class="page-subtitle">{{ $customer->unvan }} - Firma bilgilerini görüntüleyin</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn-material btn-material-warning">
            <span class="material-icons">edit</span>
            Düzenle
        </a>
        <a href="{{ route('admin.customers.index') }}" class="btn-material btn-material-secondary">
            <span class="material-icons">arrow_back</span>
            Geri
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="material-card-elevated">
            <div class="material-card-header">
                <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">business</span>Firma Bilgileri</h5>
            </div>
            <div class="material-card-body">
                <div class="material-info-grid">
                    <div class="material-info-item">
                        <span class="material-info-label">ID</span>
                        <span class="material-info-value">{{ $customer->id }}</span>
                    </div>
                    <div class="material-info-item">
                        <span class="material-info-label">Firma ID</span>
                        <span class="material-info-value"><code style="background: #f5f5f5; padding: 4px 8px; border-radius: 4px">{{ $customer->firma_id }}</code></span>
                    </div>
                    <div class="material-info-item">
                        <span class="material-info-label">Ünvan</span>
                        <span class="material-info-value">{{ $customer->unvan }}</span>
                    </div>
                    <div class="material-info-item">
                        <span class="material-info-label">Telefon</span>
                        <span class="material-info-value">{{ $customer->phone }}</span>
                    </div>
                    <div class="material-info-item">
                        <span class="material-info-label">E-posta</span>
                        <span class="material-info-value">{{ $customer->email }}</span>
                    </div>
                    <div class="material-info-item">
                        <span class="material-info-label">Oluşturulma</span>
                        <span class="material-info-value">{{ $customer->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="material-card-elevated">
            <div class="material-card-header">
                <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">account_balance</span>Mali Bilgiler</h5>
            </div>
            <div class="material-card-body">
                <div class="material-info-grid">
                    <div class="material-info-item">
                        <span class="material-info-label">Vergi Dairesi</span>
                        <span class="material-info-value">{{ $customer->vergi_dairesi }}</span>
                    </div>
                    <div class="material-info-item">
                        <span class="material-info-label">Vergi Numarası</span>
                        <span class="material-info-value">{{ $customer->vergi_numarasi }}</span>
                    </div>
                    <div class="material-info-item" style="grid-column: 1 / -1">
                        <span class="material-info-label">Bakiye</span>
                        <span class="material-badge material-badge-{{ $customer->balance > 0 ? 'success' : ($customer->balance < 0 ? 'danger' : 'secondary') }}" style="font-size: 16px; padding: 8px 16px">
                            {{ $customer->formatted_balance }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="material-card-elevated mt-4">
    <div class="material-card-header">
        <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">location_on</span>Adres</h5>
    </div>
    <div class="material-card-body">
        <p style="margin: 0; color: var(--md-text-primary)">{{ $customer->adres }}</p>
    </div>
</div>

@if($customer->users->count() > 0)
    <div class="material-card-elevated mt-4">
        <div class="material-card-header">
            <h5>
                <span class="material-icons" style="vertical-align:middle;margin-right:8px">people</span>
                Bu Firmaya Atanmış Kullanıcılar
                <span class="material-badge material-badge-primary ms-2">{{ $customer->users->count() }}</span>
            </h5>
        </div>
        <div class="material-card-body" style="padding: 0">
            <div class="material-table-wrapper" style="box-shadow: none; border-radius: 0">
                <table class="material-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ad</th>
                            <th>E-posta</th>
                            <th>Kayıt Tarihi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customer->users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at->format('d.m.Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <div class="material-card-elevated mt-4">
        <div class="material-card-body">
            <div style="text-align: center; padding: 40px">
                <span class="material-icons" style="font-size: 48px; color: #bdbdbd">person_off</span>
                <p class="text-muted mt-2 mb-0">Bu firmaya henüz kullanıcı atanmamış.</p>
            </div>
        </div>
    </div>
@endif
@endsection
