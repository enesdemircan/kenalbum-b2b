@extends('admin.layout')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Banka Hesabı Düzenle</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.bank-accounts.update', $bankAccount->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="bank_name" class="form-label">Banka Adı *</label>
                            <input type="text" name="bank_name" id="bank_name" class="form-control @error('bank_name') is-invalid @enderror" value="{{ old('bank_name', $bankAccount->bank_name) }}" required>
                            @error('bank_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="iban" class="form-label">IBAN *</label>
                            <input type="text" name="iban" id="iban" class="form-control @error('iban') is-invalid @enderror" value="{{ old('iban', $bankAccount->iban) }}" required placeholder="TR00 0000 0000 0000 0000 0000 00">
                            @error('iban')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="account_name" class="form-label">Hesap Sahibi *</label>
                            <input type="text" name="account_name" id="account_name" class="form-control @error('account_name') is-invalid @enderror" value="{{ old('account_name', $bankAccount->account_name) }}" required>
                            @error('account_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $bankAccount->is_active) ? 'checked' : '' }}>
                                <label for="is_active" class="form-check-label">Aktif</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.bank-accounts.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Geri
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Güncelle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 