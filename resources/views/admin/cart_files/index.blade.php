@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1 class="page-title">Cart Dosyaları</h1>
        <p class="page-subtitle">Sepet dosya yüklemelerini yönetin</p>
    </div>
    <button type="button" class="btn-material btn-material-danger" onclick="clearFailedFiles()">
        <span class="material-icons">delete_sweep</span>
        Başarısız Dosyaları Temizle
    </button>
</div>

<!-- İstatistikler -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="material-card-outlined p-3">
            <div class="d-flex align-items-center gap-3">
                <span class="material-icons" style="font-size: 48px; color: var(--md-info);">description</span>
                <div>
                    <div class="text-muted small">Toplam</div>
                    <div class="h4 mb-0">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="material-card-outlined p-3">
            <div class="d-flex align-items-center gap-3">
                <span class="material-icons" style="font-size: 48px; color: var(--md-warning);">schedule</span>
                <div>
                    <div class="text-muted small">Bekleyen</div>
                    <div class="h4 mb-0">{{ $stats['pending'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="material-card-outlined p-3">
            <div class="d-flex align-items-center gap-3">
                <span class="material-icons" style="font-size: 48px; color: var(--md-success);">check_circle</span>
                <div>
                    <div class="text-muted small">Tamamlanan</div>
                    <div class="h4 mb-0">{{ $stats['completed'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="material-card-outlined p-3">
            <div class="d-flex align-items-center gap-3">
                <span class="material-icons" style="font-size: 48px; color: var(--md-danger);">error</span>
                <div>
                    <div class="text-muted small">Başarısız</div>
                    <div class="h4 mb-0">{{ $stats['failed'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(count($cartFiles) > 0)
    <div class="material-card-elevated">
        <div class="material-card-header">
            <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">list</span>Dosya Listesi</h5>
        </div>
        <div class="material-card-body p-0">
            <div class="material-table-wrapper">
                <table class="material-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cart ID</th>
                            <th>Kullanıcı</th>
                            <th>Ürün</th>
                            <th>Dosya Adı</th>
                            <th>Boyut</th>
                            <th>Durum</th>
                            <th>Oluşturulma</th>
                            <th style="width: 120px">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cartFiles as $cartFile)
                            <tr>
                                <td>{{ $cartFile->id }}</td>
                                <td>
                                    <a href="#" onclick="showCartDetails({{ $cartFile->cart_id }})" class="text-primary">
                                        {{ $cartFile->cart_id }}
                                    </a>
                                </td>
                                <td>
                                    @if($cartFile->cart && $cartFile->cart->user)
                                        {{ $cartFile->cart->user->name }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($cartFile->cart && $cartFile->cart->product)
                                        {{ $cartFile->cart->product->title }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="material-icons text-muted me-2" style="font-size: 20px;">description</span>
                                        <div>
                                            <div class="fw-bold">{{ $cartFile->original_filename }}</div>
                                            <small class="text-muted">{{ $cartFile->file_type }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="material-badge material-badge-secondary">{{ number_format($cartFile->file_size / 1024, 2) }} KB</span>
                                </td>
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
                                <td><small>{{ $cartFile->created_at->format('d.m.Y H:i') }}</small></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn-material-icon btn-material-icon-info" onclick="showFileDetails({{ $cartFile->id }})" title="Detaylar">
                                            <span class="material-icons">visibility</span>
                                        </button>
                                        @if($cartFile->status === 'failed')
                                            <button type="button" class="btn-material-icon btn-material-icon-warning" onclick="retryFile({{ $cartFile->id }})" title="Yeniden Dene">
                                                <span class="material-icons">refresh</span>
                                            </button>
                                        @endif
                                        <button type="button" class="btn-material-icon btn-material-icon-danger" onclick="deleteFile({{ $cartFile->id }})" title="Sil">
                                            <span class="material-icons">delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="material-pagination mt-3">
        {{ $cartFiles->links() }}
    </div>
@else
    <div class="material-card-elevated">
        <div class="material-card-body text-center py-5">
            <span class="material-icons" style="font-size: 80px; color: var(--md-text-disabled);">folder_open</span>
            <h5 class="text-muted mt-3">Cart dosyası bulunamadı</h5>
            <p class="text-muted">Henüz hiç dosya yüklenmemiş.</p>
        </div>
    </div>
@endif

<!-- File Details Modal -->
<div class="modal fade" id="fileDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-material">
        <div class="modal-content material-modal">
            <div class="modal-header material-modal-header">
                <h5 class="modal-title">Dosya Detayları</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body material-modal-body" id="fileDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function showFileDetails(fileId) {
    fetch(`/admin/cart-files/${fileId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('fileDetailsContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('fileDetailsModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Hata!', 'Dosya detayları yüklenirken hata oluştu.', 'error');
        });
}

function retryFile(fileId) {
    Swal.fire({
        title: 'Emin misiniz?',
        text: 'Bu dosyayı yeniden yüklemek istediğinizden emin misiniz?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Evet, Yeniden Dene!',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/cart-files/${fileId}/retry`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Başarılı!', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Hata!', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Hata!', 'Bir hata oluştu.', 'error');
            });
        }
    });
}

function deleteFile(fileId) {
    Swal.fire({
        title: 'Emin misiniz?',
        text: 'Bu dosyayı silmek istediğinizden emin misiniz?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Evet, Sil!',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/admin/cart-files/${fileId}/delete`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Başarılı!', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Hata!', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Hata!', 'Bir hata oluştu.', 'error');
            });
        }
    });
}

function clearFailedFiles() {
    Swal.fire({
        title: 'Emin misiniz?',
        text: 'Tüm başarısız dosyaları silmek istediğinizden emin misiniz?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Evet, Temizle!',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('/admin/cart-files/clear-failed', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Başarılı!', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Hata!', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Hata!', 'Bir hata oluştu.', 'error');
            });
        }
    });
}

function showCartDetails(cartId) {
    Swal.fire({
        title: 'Cart Detayları',
        text: `Cart ID: ${cartId}`,
        icon: 'info'
    });
}
</script>
@endsection
