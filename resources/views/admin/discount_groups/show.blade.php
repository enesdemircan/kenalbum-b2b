@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">İndirim Grubu Detayı</h1>
        <p class="page-subtitle">{{ $discountGroup->name }} - Kampanya bilgilerini görüntüleyin</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.discount-groups.edit', $discountGroup) }}" class="btn-material btn-material-warning">
            <span class="material-icons">edit</span>
            Düzenle
        </a>
        <a href="{{ route('admin.discount-groups.index') }}" class="btn-material btn-material-secondary">
            <span class="material-icons">arrow_back</span>
            Geri Dön
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8">
        <div class="material-card-elevated">
            <div class="material-card-header">
                <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">local_offer</span>İndirim Grubu Bilgileri</h5>
            </div>
            <div class="material-card-body">
                <div class="material-info-grid">
                    <div class="material-info-item">
                        <span class="material-info-label">Ad</span>
                        <span class="material-info-value">{{ $discountGroup->name }}</span>
                    </div>
                    <div class="material-info-item">
                        <span class="material-info-label">İndirim Oranı</span>
                        <span class="material-badge material-badge-warning">%{{ $discountGroup->discount_percentage }}</span>
                    </div>
                    <div class="material-info-item">
                        <span class="material-info-label">Ana Kategori</span>
                        <span class="material-info-value">{{ $discountGroup->mainCategory->title }}</span>
                    </div>
                    <div class="material-info-item">
                        <span class="material-info-label">Durum</span>
                        @if($discountGroup->is_active)
                            <span class="material-badge material-badge-success">Aktif</span>
                        @else
                            <span class="material-badge material-badge-danger">Pasif</span>
                        @endif
                    </div>
                    <div class="material-info-item">
                        <span class="material-info-label">Başlangıç Tarihi</span>
                        <span class="material-info-value">{{ $discountGroup->start_date ? $discountGroup->start_date->format('d.m.Y') : 'Belirtilmemiş' }}</span>
                    </div>
                    <div class="material-info-item">
                        <span class="material-info-label">Bitiş Tarihi</span>
                        <span class="material-info-value">{{ $discountGroup->end_date ? $discountGroup->end_date->format('d.m.Y') : 'Belirtilmemiş' }}</span>
                    </div>
                    <div class="material-info-item">
                        <span class="material-info-label">Firmalar</span>
                        <p class="mb-0">
                            @if($discountGroup->customers->count() > 0)
                                @foreach($discountGroup->customers as $customer)
                                    <span class="material-badge material-badge-primary">{{ $customer->unvan }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">Firma Yok</span>
                            @endif
                        </p>
                    </div>
                    <div class="material-info-item">
                        <span class="material-info-label">Oluşturulma</span>
                        <span class="material-info-value">{{ $discountGroup->created_at->format('d.m.Y H:i') }}</span>
                    </div>
                </div>
                
                @if($discountGroup->description)
                    <div class="mt-4 pt-3 border-top">
                        <span class="material-info-label">Açıklama</span>
                        <p class="mt-2 mb-0">{{ $discountGroup->description }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="material-card-elevated">
            <div class="material-card-header">
                <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">analytics</span>İstatistikler</h5>
            </div>
            <div class="material-card-body">
                @php
                    $totalUsers = 0;
                    foreach($discountGroup->customers as $customer) {
                        $totalUsers += $customer->users->count();
                    }
                @endphp
                <div class="material-info-grid">
                    <div class="material-info-item">
                        <span class="material-info-label">Toplam Kullanıcı</span>
                        <span class="material-info-value">{{ $totalUsers }}</span>
                    </div>
                    <div class="material-info-item">
                        <span class="material-info-label">Firma Sayısı</span>
                        <span class="material-info-value">{{ $discountGroup->customers->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="material-card-elevated mt-4">
    <div class="material-card-header">
        <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">people</span>Firma Kullanıcıları</h5>
    </div>
    <div class="material-card-body" style="padding: 0">
        @if($discountGroup->customers->count() > 0)
            <div class="material-table-wrapper" style="box-shadow: none; border-radius: 0">
                <table class="material-table">
                    <thead>
                        <tr>
                            <th>Firma</th>
                            <th>Kullanıcı</th>
                            <th>E-posta</th>
                            <th>Roller</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($discountGroup->customers as $customer)
                            @foreach($customer->users as $user)
                                <tr>
                                    <td><strong>{{ $customer->unvan }}</strong></td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @foreach($user->roles as $role)
                                            <span class="material-badge material-badge-secondary">{{ $role->name }}</span>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-4 text-center">
                <span class="material-icons" style="font-size: 48px; color: #bdbdbd">person_off</span>
                <p class="text-muted mt-2 mb-0">Bu gruba henüz firma eklenmemiş.</p>
            </div>
        @endif
    </div>
</div>
@endsection
