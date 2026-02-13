@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Yeni Kullanıcı Ekle</h1>
        <p class="page-subtitle">Sisteme yeni kullanıcı kaydı oluşturun</p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn-material btn-material-secondary">
        <span class="material-icons">arrow_back</span>
        Geri Dön
    </a>
</div>

@if(session('error'))
    <div class="material-alert material-alert-danger">
        <span class="material-icons">error</span>
        <span>{{ session('error') }}</span>
    </div>
@endif

<div class="material-card-elevated">
    <div class="material-card-header">
        <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">person_add</span>Kullanıcı Bilgileri</h5>
    </div>
    <div class="material-card-body">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Ad Soyad *</label>
                        <input type="text" class="form-control form-control-material @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">E-posta *</label>
                        <input type="email" class="form-control form-control-material @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="password" class="form-label">Şifre *</label>
                        <input type="password" class="form-control form-control-material @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Şifre Tekrar *</label>
                        <input type="password" class="form-control form-control-material @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" required>
                        @error('password_confirmation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="roles" class="form-label">Roller *</label>
                        <select class="form-select form-control-material @error('roles') is-invalid @enderror" id="roles" name="roles[]" multiple required style="min-height: 120px">
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ in_array($role->id, old('roles', [])) ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('roles')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            <span class="material-icons" style="font-size: 14px; vertical-align: middle">info</span>
                            Ctrl tuşu ile birden fazla rol seçebilirsiniz.
                        </small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="customer_id" class="form-label">Firma</label>
                        <select class="form-select form-control-material @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id">
                            <option value="">Firma Seçin</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->unvan }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="status" class="form-label">Durum *</label>
                        <select class="form-select form-control-material @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Onaylandı</option>
                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Onay Bekliyor</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('admin.users.index') }}" class="btn-material btn-material-secondary">
                    <span class="material-icons">close</span>
                    İptal
                </a>
                <button type="submit" class="btn-material btn-material-primary">
                    <span class="material-icons">save</span>
                    Kullanıcı Oluştur
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
