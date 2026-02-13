@extends('admin.layout')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Yeni Tahsilat Ekle</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.collections.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="customer_id" class="form-label">Firma *</label>
                                    <select name="customer_id" id="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                                        <option value="">Firma seçin...</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
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
                                    <label for="amount" class="form-label">Tahsilat Miktarı (₺) *</label>
                                    <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" step="0.01" min="0.01" max="999999999.99" placeholder="0.00" required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Tahsilat Şekli *</label>
                                    <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                                        <option value="">Seçin...</option>
                                        <option value="kredi_karti" {{ old('payment_method') == 'kredi_karti' ? 'selected' : '' }}>Kredi Kartı</option>
                                        <option value="havale" {{ old('payment_method') == 'havale' ? 'selected' : '' }}>Havale</option>
                                        <option value="nakit" {{ old('payment_method') == 'nakit' ? 'selected' : '' }}>Nakit</option>
                                    </select>
                                    @error('payment_method')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="collection_date" class="form-label">Tahsilat Tarihi *</label>
                                    <input type="date" name="collection_date" id="collection_date" class="form-control @error('collection_date') is-invalid @enderror" value="{{ old('collection_date', date('Y-m-d')) }}" required>
                                    @error('collection_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notlar</label>
                            <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" placeholder="Tahsilat ile ilgili notlar...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Bilgi:</strong> Tahsilat eklendiğinde otomatik olarak firma bakiyesine eklenecektir.
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.collections.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Geri
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 