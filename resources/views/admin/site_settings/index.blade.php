@extends('admin.layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Site Ayarları</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Genel Ayarlar</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.site-settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Logo -->
                    <div class="col-md-6 mb-3">
                        <label for="logo" class="form-label">Logo</label>
                        @if($settings->logo)
                            <div class="mb-2">
                                <img src="{{ $settings->logo }}" alt="Logo" style="max-height: 100px;">
                            </div>
                        @endif
                        <input type="file" name="logo" id="logo" class="form-control" accept="image/*">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="logo_white" class="form-label">Logo (Beyaz)</label>
                        @if($settings->logo_white)
                            <div class="mb-2">
                                <img src="{{$settings->logo_white }}" alt="Logo (Beyaz)" style="max-height: 100px;">
                            </div>
                        @endif
                        <input type="file" name="logo_white" id="logo_white" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="favicon" class="form-label">Favicon</label>
                        @if($settings->favicon)
                            <div class="mb-2">
                                <img src="{{ $settings->favicon }}" alt="Favicon" style="max-height: 100px;">
                            </div>
                        @endif
                        <input type="file" name="favicon" id="favicon" class="form-control" accept="image/*">
                    </div>
                  

                    <!-- Site Başlığı -->
                    <div class="col-md-6 mb-3">
                        <label for="title" class="form-label">Site Başlığı</label>
                        <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $settings->title) }}">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Açıklama -->
                    <div class="col-12 mb-3">
                        <label for="description" class="form-label">Site Açıklaması</label>
                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description', $settings->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- İletişim Bilgileri -->
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Telefon</label>
                        <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $settings->phone) }}">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">E-posta</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $settings->email) }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <label for="address" class="form-label">Adres</label>
                        <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="3">{{ old('address', $settings->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Sosyal Medya Linkleri -->
                    <div class="col-md-6 mb-3">
                        <label for="facebook" class="form-label">Facebook Linki</label>
                        <input type="url" name="facebook" id="facebook" class="form-control @error('facebook') is-invalid @enderror" value="{{ old('facebook', $settings->facebook) }}">
                        @error('facebook')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="instagram" class="form-label">Instagram Linki</label>
                        <input type="url" name="instagram" id="instagram" class="form-control @error('instagram') is-invalid @enderror" value="{{ old('instagram', $settings->instagram) }}">
                        @error('instagram')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="twitter" class="form-label">Twitter Linki</label>
                        <input type="url" name="twitter" id="twitter" class="form-control @error('twitter') is-invalid @enderror" value="{{ old('twitter', $settings->twitter) }}">
                        @error('twitter')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="youtube" class="form-label">YouTube Linki</label>
                        <input type="url" name="youtube" id="youtube" class="form-control @error('youtube') is-invalid @enderror" value="{{ old('youtube', $settings->youtube) }}">
                        @error('youtube')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Firma Bilgileri -->
                    <div class="col-md-6 mb-3">
                        <label for="company_title" class="form-label">Firma Ünvanı</label>
                        <input type="text" name="company_title" id="company_title" class="form-control @error('company_title') is-invalid @enderror" value="{{ old('company_title', $settings->company_title) }}">
                        @error('company_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="tax_rate" class="form-label">Vergi Oranı (%)</label>
                        <input type="number" name="tax_rate" id="tax_rate" class="form-control @error('tax_rate') is-invalid @enderror" value="{{ old('tax_rate', $settings->tax_rate) }}" min="0" max="100" step="0.01">
                        @error('tax_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Duyuru -->
                    <div class="col-12 mb-3">
                        <label for="announcement" class="form-label">Duyuru</label>
                        <textarea name="announcement" id="announcement" class="form-control @error('announcement') is-invalid @enderror" rows="4">{{ old('announcement', $settings->announcement) }}</textarea>
                        @error('announcement')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Ayarları Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection 