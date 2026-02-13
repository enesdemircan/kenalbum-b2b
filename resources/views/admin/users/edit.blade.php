@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Kullanıcı Düzenle</h1>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Geri Dön
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">{{ $user->name }} - Kullanıcı Bilgilerini Düzenle</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="name" class="form-label">Ad Soyad *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">E-posta *</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="customer_id" class="form-label">Firma</label>
                        <select class="form-select @error('customer_id') is-invalid @enderror" 
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
                        <select class="form-select @error('status') is-invalid @enderror" 
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

            <div class="mb-3">
                <label class="form-label">Roller</label>
                <div class="row">
                    @foreach($roles as $role)
                        <div class="col-md-3">
                            <div class="form-check">
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

            <div class="alert alert-info">
                <strong>Bilgi:</strong> Şifre değişikliği bu form üzerinden yapılamaz. Şifre sıfırlama için kullanıcıya e-posta gönderilmelidir.
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Güncelle
                </button>
                
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="bi bi-trash"></i> Kullanıcıyı Sil
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Silme Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kullanıcıyı Sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>{{ $user->name }}</strong> adlı kullanıcıyı silmek istediğinizden emin misiniz?</p>
                <div class="alert alert-warning">
                    <strong>Uyarı:</strong> Bu işlem geri alınamaz. Kullanıcının tüm verileri kalıcı olarak silinecektir.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Evet, Sil</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection 