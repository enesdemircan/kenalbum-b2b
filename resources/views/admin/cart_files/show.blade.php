<div class="row">
    <div class="col-md-6">
        <div class="material-card-outlined p-3 mb-3">
            <h6 class="mb-3"><span class="material-icons" style="vertical-align:middle;font-size:20px;margin-right:4px">description</span>Dosya Bilgileri</h6>
            <table class="table table-sm table-borderless mb-0">
                <tr>
                    <td class="text-muted" style="width: 140px;"><strong>ID:</strong></td>
                    <td>{{ $cartFile->id }}</td>
                </tr>
                <tr>
                    <td class="text-muted"><strong>Dosya Adı:</strong></td>
                    <td>{{ $cartFile->original_filename }}</td>
                </tr>
                <tr>
                    <td class="text-muted"><strong>Dosya Tipi:</strong></td>
                    <td>{{ $cartFile->file_type }}</td>
                </tr>
                <tr>
                    <td class="text-muted"><strong>Dosya Boyutu:</strong></td>
                    <td>{{ number_format($cartFile->file_size / 1024, 2) }} KB</td>
                </tr>
                <tr>
                    <td class="text-muted"><strong>Durum:</strong></td>
                    <td>
                        @if($cartFile->status === 'pending')
                            <span class="material-badge material-badge-warning">Bekliyor</span>
                        @elseif($cartFile->status === 'uploading')
                            <span class="material-badge material-badge-info">Yükleniyor</span>
                        @elseif($cartFile->status === 'completed')
                            <span class="material-badge material-badge-success">Tamamlandı</span>
                        @elseif($cartFile->status === 'failed')
                            <span class="material-badge material-badge-danger">Başarısız</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="text-muted"><strong>Oluşturulma:</strong></td>
                    <td>{{ $cartFile->created_at->format('d.m.Y H:i:s') }}</td>
                </tr>
                <tr>
                    <td class="text-muted"><strong>Güncellenme:</strong></td>
                    <td>{{ $cartFile->updated_at->format('d.m.Y H:i:s') }}</td>
                </tr>
            </table>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="material-card-outlined p-3 mb-3">
            <h6 class="mb-3"><span class="material-icons" style="vertical-align:middle;font-size:20px;margin-right:4px">shopping_cart</span>Cart Bilgileri</h6>
            @if($cartFile->cart)
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <td class="text-muted" style="width: 140px;"><strong>Cart ID:</strong></td>
                        <td>{{ $cartFile->cart_id }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Kullanıcı:</strong></td>
                        <td>
                            @if($cartFile->cart->user)
                                {{ $cartFile->cart->user->name }} ({{ $cartFile->cart->user->email }})
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Ürün:</strong></td>
                        <td>
                            @if($cartFile->cart->product)
                                {{ $cartFile->cart->product->title }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Fiyat:</strong></td>
                        <td>{{ $cartFile->cart->price }} TL</td>
                    </tr>
                    <tr>
                        <td class="text-muted"><strong>Adet:</strong></td>
                        <td>{{ $cartFile->cart->quantity }}</td>
                    </tr>
                </table>
            @else
                <p class="text-muted mb-0">Cart bilgisi bulunamadı.</p>
            @endif
        </div>
    </div>
</div>

@if($cartFile->customizationPivotParam)
<div class="material-card-outlined p-3 mb-3">
    <h6 class="mb-3"><span class="material-icons" style="vertical-align:middle;font-size:20px;margin-right:4px">tune</span>Özelleştirme Parametresi</h6>
    <table class="table table-sm table-borderless mb-0">
        <tr>
            <td class="text-muted" style="width: 140px;"><strong>Parametre ID:</strong></td>
            <td>{{ $cartFile->customizationPivotParam->id }}</td>
        </tr>
        <tr>
            <td class="text-muted"><strong>Parametre Adı:</strong></td>
            <td>{{ $cartFile->customizationPivotParam->param->key ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="text-muted"><strong>Fiyat:</strong></td>
            <td>{{ $cartFile->customizationPivotParam->price ?? 0 }} TL</td>
        </tr>
    </table>
</div>
@endif

@if($cartFile->local_file_url)
<div class="material-card-outlined p-3 mb-3">
    <h6 class="mb-2"><span class="material-icons" style="vertical-align:middle;font-size:20px;margin-right:4px">folder</span>Local Dosya</h6>
    <p class="mb-1"><strong>URL:</strong> {{ $cartFile->local_file_url }}</p>
    @if(file_exists($cartFile->local_file_url))
        <span class="material-badge material-badge-success">Dosya mevcut</span>
    @else
        <span class="material-badge material-badge-danger">Dosya bulunamadı</span>
    @endif
</div>
@endif

@if($cartFile->s3_url)
<div class="material-card-outlined p-3 mb-3">
    <h6 class="mb-2"><span class="material-icons" style="vertical-align:middle;font-size:20px;margin-right:4px">cloud</span>S3 Dosya</h6>
    <p class="mb-2"><strong>URL:</strong> {{ $cartFile->s3_url }}</p>
    <a href="{{ $cartFile->s3_url }}" target="_blank" class="btn-material btn-material-primary" style="padding: 8px 16px; font-size: 13px;">
        <span class="material-icons" style="font-size:18px">open_in_new</span>
        Görüntüle
    </a>
</div>
@endif

@if($cartFile->error_message)
<div class="material-alert material-alert-danger">
    <span class="material-icons">error</span>
    <div>
        <strong>Hata Mesajı</strong>
        <pre class="mb-0 mt-1" style="white-space: pre-wrap; font-size: 12px;">{{ $cartFile->error_message }}</pre>
    </div>
</div>
@endif
