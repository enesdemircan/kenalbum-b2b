@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Kullanıcı Düzenle</h1>
        <p class="page-subtitle">{{ $user->name }} - Kullanıcı bilgilerini güncelleyin</p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="btn-material btn-material-secondary">
        <span class="material-icons">arrow_back</span>
        Geri Dön
    </a>
</div>

@if(session('success'))
    <div class="material-alert material-alert-success">
        <span class="material-icons">check_circle</span>
        <span>{{ session('success') }}</span>
    </div>
@endif

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
    <div class="material-card-header">
        <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">edit</span>Kullanıcı Bilgilerini Düzenle</h5>
    </div>
    <div class="material-card-body">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Ad Soyad *</label>
                        <input type="text" class="form-control form-control-material @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">E-posta *</label>
                        <input type="email" class="form-control form-control-material @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="customer_id" class="form-label">Firma</label>
                        <select class="form-select form-control-material @error('customer_id') is-invalid @enderror" 
                                id="customer_id" name="customer_id">
                            <option value="">Firma Seçin</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" 
                                    {{ old('customer_id', $user->customer_id) == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->unvan }} ({{ $customer->firma_id }})
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="status" class="form-label">Durum *</label>
                        <select class="form-select form-control-material @error('status') is-invalid @enderror" 
                                id="status" name="status" required>
                            <option value="0" {{ old('status', $user->status) == 0 ? 'selected' : '' }}>Onay Bekliyor</option>
                            <option value="1" {{ old('status', $user->status) == 1 ? 'selected' : '' }}>Onaylandı</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Roller</label>
                <div class="row">
                    @foreach($roles as $role)
                        <div class="col-md-3">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="roles[]" 
                                       value="{{ $role->id }}" id="role_{{ $role->id }}"
                                       {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
                                <label class="form-check-label" for="role_{{ $role->id }}">
                                    {{ $role->name }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="material-alert material-alert-info mb-4">
                <span class="material-icons">info</span>
                <span><strong>Bilgi:</strong> Şifre değişikliği bu form üzerinden yapılamaz. Şifre sıfırlama için kullanıcıya e-posta gönderilmelidir.</span>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn-material btn-material-primary">
                    <span class="material-icons">save</span>
                    Güncelle
                </button>
                
                <button type="button" class="btn-material btn-material-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <span class="material-icons">delete</span>
                    Kullanıcıyı Sil
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Silme Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-material">
        <div class="modal-content material-modal">
            <div class="modal-header material-modal-header">
                <h5 class="modal-title">
                    <span class="material-icons" style="vertical-align:middle;margin-right:8px">warning</span>
                    Kullanıcıyı Sil
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body material-modal-body">
                <p><strong>{{ $user->name }}</strong> adlı kullanıcıyı silmek istediğinizden emin misiniz?</p>
                <div class="material-alert material-alert-warning">
                    <span class="material-icons">warning</span>
                    <span><strong>Uyarı:</strong> Bu işlem geri alınamaz. Kullanıcının tüm verileri kalıcı olarak silinecektir.</span>
                </div>
            </div>
            <div class="modal-footer material-modal-footer">
                <button type="button" class="btn-material btn-material-secondary" data-bs-dismiss="modal">İptal</button>
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-material btn-material-danger">
                        <span class="material-icons">delete_forever</span>
                        Evet, Sil
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
