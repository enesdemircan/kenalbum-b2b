@extends('admin.layout')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Yeni Tahsilat Ekle</h5>
                    <p class="text-muted mb-0">{{ $customer->unvan }} firması için</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.customers.collections.store', $customer->id) }}" method="POST">
                        @csrf
                        
                        <!-- Firma Bilgileri -->
                        <div class="alert alert-info mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Firma:</strong> {{ $customer->unvan }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Mevcut Bakiye:</strong> 
                                    <span class="badge {{ $customer->balance > 0 ? 'bg-success' : ($customer->balance < 0 ? 'bg-danger' : 'bg-secondary') }}">
                                        {{ $customer->formatted_balance }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Tahsilat Miktarı (₺) *</label>
                                    <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" step="0.01" min="0.01" max="999999999.99" placeholder="0.00" required>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
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
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="collection_date" class="form-label">Tahsilat Tarihi *</label>
                                    <input type="date" name="collection_date" id="collection_date" class="form-control @error('collection_date') is-invalid @enderror" value="{{ old('collection_date', date('Y-m-d')) }}" required>
                                    @error('collection_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notlar</label>
                                    <textarea name="notes" id="notes" rows="1" class="form-control @error('notes') is-invalid @enderror" placeholder="Tahsilat ile ilgili notlar...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-success">
                            <i class="bi bi-check-circle"></i>
                            <strong>Bilgi:</strong> Tahsilat eklendiğinde otomatik olarak <strong>{{ $customer->unvan }}</strong> firmasının bakiyesine eklenecektir.
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.customers.collections', $customer->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Geri
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Tahsilat Ekle
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection 