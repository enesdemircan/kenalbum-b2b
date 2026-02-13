@extends('frontend.master')

@section('content')
<main>
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
        <h2 class="page-title">Personellerim</h2>
        <div class="row">
            <div class="col-lg-3">
                <ul class="account-nav">
                    <li><a href="{{ route('profile.index') }}" class="menu-link menu-link_us-s">DASHBOARD</a></li>
                    <li><a href="{{ route('profile.orders') }}" class="menu-link menu-link_us-s">SİPARİŞLERİM</a></li>
                    <li><a href="{{ route('profile.addresses') }}" class="menu-link menu-link_us-s">ADRESLERİM</a></li>
                    <li><a href="{{ route('profile.detail') }}" class="menu-link menu-link_us-s">HESAP DETAYLARI</a></li>
                    @if(auth()->user()->hasRole(1))
                    <li><a href="{{ route('admin.dashboard') }}" class="menu-link menu-link_us-s">YÖNETİM PANELİ</a></li>
                    @endif
                    @if(auth()->user()->hasRole(3))
                    <li><a href="{{ route('profile.personels') }}" class="menu-link menu-link_us-s menu-link_active">PERSONELLERİM</a></li>
                    @endif
                    <li>
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="menu-link menu-link_us-s border-0 bg-transparent" style="width: 100%; text-align: left; padding: 0; font-size: 14px;font-weight: 500;text-transform: uppercase;">
                                Çıkış Yap
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            <div class="col-lg-9">
                <div class="page-content my-account__dashboard">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h3>Personel Listesi</h3>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPersonelModal">
                            <i class="fas fa-plus"></i> Yeni Personel Ekle
                        </button>
                    </div>
                    
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if($personels->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Ad Soyad</th>
                                        <th>E-posta</th>
                                        <th>Roller</th>
                                        <th>Kayıt Tarihi</th>
                                        <th style="width: 150px;">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($personels as $personel)
                                        <tr>
                                            <td>{{ $personel->id }}</td>
                                            <td>{{ $personel->name }}</td>
                                            <td>{{ $personel->email }}</td>
                                            <td>
                                                @foreach($personel->roles as $role)
                                                    <span class="badge bg-primary">{{ $role->name }}</span>
                                                @endforeach
                                            </td>
                                            <td>{{ $personel->created_at->format('d.m.Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('profile.personels.orders', $personel->id) }}" class="btn btn-sm btn-info" title="Siparişleri Görüntüle">
                                                        <i class="fas fa-shopping-cart"></i>
                                                    </a>
                                                    <form action="{{ route('profile.personels.delete', $personel->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('{{ $personel->name }} adlı personeli silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Personeli Sil">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            <p class="text-muted">Toplam {{ $personels->count() }} personel bulundu.</p>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Henüz personel atanmamış.
                            </div>
                            <p class="text-muted">Firmanıza henüz başka kullanıcı atanmamış.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Personel Ekleme Modal -->
<div class="modal fade" id="addPersonelModal" tabindex="-1" aria-labelledby="addPersonelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPersonelModalLabel">Yeni Personel Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('profile.personels.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Ad Soyad *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">E-posta *</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Şifre *</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Şifre Tekrar *</label>
                        <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" required>
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Personel Ekle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<br>
<br>
@endsection