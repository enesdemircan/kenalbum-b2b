<div class="row">
    <div class="col-md-6">
        <h6>Dosya Bilgileri</h6>
        <table class="table table-sm">
            <tr>
                <td><strong>ID:</strong></td>
                <td>{{ $cartFile->id }}</td>
            </tr>
            <tr>
                <td><strong>Dosya Adı:</strong></td>
                <td>{{ $cartFile->original_filename }}</td>
            </tr>
            <tr>
                <td><strong>Dosya Tipi:</strong></td>
                <td>{{ $cartFile->file_type }}</td>
            </tr>
            <tr>
                <td><strong>Dosya Boyutu:</strong></td>
                <td>{{ number_format($cartFile->file_size / 1024, 2) }} KB</td>
            </tr>
            <tr>
                <td><strong>Durum:</strong></td>
                <td>
                    @if($cartFile->status === 'pending')
                        <span class="badge bg-warning">Bekliyor</span>
                    @elseif($cartFile->status === 'uploading')
                        <span class="badge bg-info">Yükleniyor</span>
                    @elseif($cartFile->status === 'completed')
                        <span class="badge bg-success">Tamamlandı</span>
                    @elseif($cartFile->status === 'failed')
                        <span class="badge bg-danger">Başarısız</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td><strong>Oluşturulma:</strong></td>
                <td>{{ $cartFile->created_at->format('d.m.Y H:i:s') }}</td>
            </tr>
            <tr>
                <td><strong>Güncellenme:</strong></td>
                <td>{{ $cartFile->updated_at->format('d.m.Y H:i:s') }}</td>
            </tr>
        </table>
    </div>
    
    <div class="col-md-6">
        <h6>Cart Bilgileri</h6>
        @if($cartFile->cart)
            <table class="table table-sm">
                <tr>
                    <td><strong>Cart ID:</strong></td>
                    <td>{{ $cartFile->cart_id }}</td>
                </tr>
                <tr>
                    <td><strong>Kullanıcı:</strong></td>
                    <td>
                        @if($cartFile->cart->user)
                            {{ $cartFile->cart->user->name }} ({{ $cartFile->cart->user->email }})
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><strong>Ürün:</strong></td>
                    <td>
                        @if($cartFile->cart->product)
                            {{ $cartFile->cart->product->title }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><strong>Fiyat:</strong></td>
                    <td>{{ $cartFile->cart->price }} TL</td>
                </tr>
                <tr>
                    <td><strong>Adet:</strong></td>
                    <td>{{ $cartFile->cart->quantity }}</td>
                </tr>
            </table>
        @else
            <p class="text-muted">Cart bilgisi bulunamadı.</p>
        @endif
    </div>
</div>

@if($cartFile->customizationPivotParam)
<div class="row mt-3">
    <div class="col-12">
        <h6>Özelleştirme Parametresi</h6>
        <table class="table table-sm">
            <tr>
                <td><strong>Parametre ID:</strong></td>
                <td>{{ $cartFile->customizationPivotParam->id }}</td>
            </tr>
            <tr>
                <td><strong>Parametre Adı:</strong></td>
                <td>{{ $cartFile->customizationPivotParam->param->key ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Fiyat:</strong></td>
                <td>{{ $cartFile->customizationPivotParam->price ?? 0 }} TL</td>
            </tr>
        </table>
    </div>
</div>
@endif

@if($cartFile->local_file_url)
<div class="row mt-3">
    <div class="col-12">
        <h6>Local Dosya</h6>
        <p><strong>URL:</strong> {{ $cartFile->local_file_url }}</p>
        @if(file_exists($cartFile->local_file_url))
            <span class="badge bg-success">Dosya mevcut</span>
        @else
            <span class="badge bg-danger">Dosya bulunamadı</span>
        @endif
    </div>
</div>
@endif

@if($cartFile->s3_url)
<div class="row mt-3">
    <div class="col-12">
        <h6>S3 Dosya</h6>
        <p><strong>URL:</strong> {{ $cartFile->s3_url }}</p>
        <a href="{{ $cartFile->s3_url }}" target="_blank" class="btn btn-sm btn-primary">
            <i class="fas fa-external-link-alt"></i> Görüntüle
        </a>
    </div>
</div>
@endif

@if($cartFile->error_message)
<div class="row mt-3">
    <div class="col-12">
        <h6>Hata Mesajı</h6>
        <div class="alert alert-danger">
            <pre>{{ $cartFile->error_message }}</pre>
        </div>
    </div>
</div>
@endif 