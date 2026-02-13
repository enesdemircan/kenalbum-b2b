@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">İndirim Grubu Detayı</h1>
    <div>
        <a href="{{ route('admin.discount-groups.edit', $discountGroup) }}" class="btn btn-warning">
            <i class="bi bi-pencil-square"></i> Düzenle
        </a>
        <a href="{{ route('admin.discount-groups.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Geri Dön
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">İndirim Grubu Bilgileri</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Ad:</strong> {{ $discountGroup->name }}</p>
                        <p><strong>İndirim Oranı:</strong> %{{ $discountGroup->discount_percentage }}</p>
                        <p><strong>Ana Kategori:</strong> {{ $discountGroup->mainCategory->title }}</p>
                        <p><strong>Firmalar:</strong> 
                            @if($discountGroup->customers->count() > 0)
                                @foreach($discountGroup->customers as $customer)
                                    <span class="badge bg-primary">{{ $customer->unvan }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">Firma Yok</span>
                            @endif
                        </p>
                        <p><strong>Durum:</strong> 
                            @if($discountGroup->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-danger">Pasif</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Başlangıç Tarihi:</strong> 
                            {{ $discountGroup->start_date ? $discountGroup->start_date->format('d.m.Y') : 'Belirtilmemiş' }}
                        </p>
                        <p><strong>Bitiş Tarihi:</strong> 
                            {{ $discountGroup->end_date ? $discountGroup->end_date->format('d.m.Y') : 'Belirtilmemiş' }}
                        </p>
                        <p><strong>Oluşturulma Tarihi:</strong> {{ $discountGroup->created_at->format('d.m.Y H:i') }}</p>
                        <p><strong>Son Güncelleme:</strong> {{ $discountGroup->updated_at->format('d.m.Y H:i') }}</p>
                    </div>
                </div>
                
                @if($discountGroup->description)
                    <div class="mt-3">
                        <strong>Açıklama:</strong>
                        <p class="mt-2">{{ $discountGroup->description }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">İstatistikler</h5>
            </div>
            <div class="card-body">
                @php
                    $totalUsers = 0;
                    foreach($discountGroup->customers as $customer) {
                        $totalUsers += $customer->users->count();
                    }
                @endphp
                <p><strong>Toplam Kullanıcı Sayısı:</strong> {{ $totalUsers }}</p>
                <p><strong>Firma Sayısı:</strong> {{ $discountGroup->customers->count() }}</p>
                <p><strong>Sipariş Sayısı:</strong> 0</p>
                <p><strong>Toplam İndirim Tutarı:</strong> 0.00 ₺</p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Firma Kullanıcıları</h5>
            </div>
            <div class="card-body">
                @if($discountGroup->customers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
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
                                                    <span class="badge bg-secondary">{{ $role->name }}</span>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Bu gruba henüz firma eklenmemiş.</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Sipariş Bilgileri</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Siparişler artık doğrudan indirim grupları ile ilişkilendirilmiyor. İndirim bilgileri cart (sepet) kayıtlarında tutulmaktadır.</p>
            </div>
        </div>
    </div>
</div>
@endsection 