@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Firma Düzenle</h1>
        <p class="page-subtitle">{{ $customer->unvan }} - Firma bilgilerini güncelleyin</p>
    </div>
    <a href="{{ route('admin.customers.index') }}" class="btn-material btn-material-secondary">
        <span class="material-icons">arrow_back</span>
        Geri Dön
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="material-card-elevated">
            <div class="material-card-header">
                <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">edit</span>Firma Bilgilerini Düzenle</h5>
            </div>
            <div class="material-card-body">
                <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="firma_id" class="form-label">Firma ID *</label>
                                <input type="text" name="firma_id" id="firma_id" class="form-control form-control-material @error('firma_id') is-invalid @enderror" value="{{ old('firma_id', $customer->firma_id) }}" required>
                                @error('firma_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unvan" class="form-label">Ünvan *</label>
                                <input type="text" name="unvan" id="unvan" class="form-control form-control-material @error('unvan') is-invalid @enderror" value="{{ old('unvan', $customer->unvan) }}" required>
                                @error('unvan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Telefon *</label>
                                <input type="text" name="phone" id="phone" class="form-control form-control-material @error('phone') is-invalid @enderror" value="{{ old('phone', $customer->phone) }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">E-posta *</label>
                                <input type="email" name="email" id="email" class="form-control form-control-material @error('email') is-invalid @enderror" value="{{ old('email', $customer->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="adres" class="form-label">Adres *</label>
                        <textarea name="adres" id="adres" rows="3" class="form-control form-control-material @error('adres') is-invalid @enderror" required>{{ old('adres', $customer->adres) }}</textarea>
                        @error('adres')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="vergi_dairesi" class="form-label">Vergi Dairesi *</label>
                                <input type="text" name="vergi_dairesi" id="vergi_dairesi" class="form-control form-control-material @error('vergi_dairesi') is-invalid @enderror" value="{{ old('vergi_dairesi', $customer->vergi_dairesi) }}" required>
                                @error('vergi_dairesi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="vergi_numarasi" class="form-label">Vergi Numarası *</label>
                                <input type="text" name="vergi_numarasi" id="vergi_numarasi" class="form-control form-control-material @error('vergi_numarasi') is-invalid @enderror" value="{{ old('vergi_numarasi', $customer->vergi_numarasi) }}" required>
                                @error('vergi_numarasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="balance" class="form-label">Bakiye (₺)</label>
                                <input type="number" name="balance" id="balance" class="form-control form-control-material @error('balance') is-invalid @enderror" value="{{ old('balance', $customer->balance ?? 0.00) }}" step="0.01" min="-999999999.99" max="999999999.99" placeholder="0.00">
                                <small class="form-text text-muted">
                                    <span class="material-icons" style="font-size: 14px; vertical-align: middle">info</span>
                                    Negatif değerler borç, pozitif değerler alacak gösterir
                                </small>
                                @error('balance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.customers.index') }}" class="btn-material btn-material-secondary">
                            <span class="material-icons">close</span>
                            İptal
                        </a>
                        <button type="submit" class="btn-material btn-material-warning">
                            <span class="material-icons">save</span>
                            Güncelle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
