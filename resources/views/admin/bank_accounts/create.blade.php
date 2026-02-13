@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Yeni Banka Hesabı Ekle</h1>
        <p class="page-subtitle">Banka hesap bilgilerini girin</p>
    </div>
    <a href="{{ route('admin.bank-accounts.index') }}" class="btn-material btn-material-secondary">
        <span class="material-icons">arrow_back</span> Geri Dön
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="material-card-elevated">
            <div class="material-card-body">
                <form action="{{ route('admin.bank-accounts.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="bank_name" class="form-label">Banka Adı *</label>
                        <input type="text" name="bank_name" id="bank_name" class="form-control form-control-material @error('bank_name') is-invalid @enderror" value="{{ old('bank_name') }}" required>
                            @error('bank_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="iban" class="form-label">IBAN *</label>
                            <input type="text" name="iban" id="iban" class="form-control form-control-material @error('iban') is-invalid @enderror" value="{{ old('iban') }}" required placeholder="TR00 0000 0000 0000 0000 0000 00">
                            @error('iban')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="account_name" class="form-label">Hesap Sahibi *</label>
                            <input type="text" name="account_name" id="account_name" class="form-control form-control-material @error('account_name') is-invalid @enderror" value="{{ old('account_name') }}" required>
                            @error('account_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active') ? 'checked' : '' }}>
                                <label for="is_active" class="form-check-label">Aktif</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn-material btn-material-success">
                                <span class="material-icons">save</span> Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 