@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Cart Dosyaları</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-danger" onclick="clearFailedFiles()">
                            <i class="fas fa-trash"></i> Başarısız Dosyaları Temizle
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- İstatistikler -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-file"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Toplam</span>
                                    <span class="info-box-number">{{ $stats['total'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Bekleyen</span>
                                    <span class="info-box-number">{{ $stats['pending'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Tamamlanan</span>
                                    <span class="info-box-number">{{ $stats['completed'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger"><i class="fas fa-times"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Başarısız</span>
                                    <span class="info-box-number">{{ $stats['failed'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(count($cartFiles) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
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
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cartFiles as $cartFile)
                                        <tr>
                                            <td>{{ $cartFile->id }}</td>
                                            <td>
                                                <a href="#" onclick="showCartDetails({{ $cartFile->cart_id }})">
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
                                                    <i class="fas fa-file me-2 text-muted"></i>
                                                    <div>
                                                        <div class="fw-bold">{{ $cartFile->original_filename }}</div>
                                                        <small class="text-muted">{{ $cartFile->file_type }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ number_format($cartFile->file_size / 1024, 2) }} KB</span>
                                            </td>
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
                                            <td>
                                                <small>{{ $cartFile->created_at->format('d.m.Y H:i') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-info btn-sm" onclick="showFileDetails({{ $cartFile->id }})" title="Detaylar">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    @if($cartFile->status === 'failed')
                                                        <button type="button" class="btn btn-warning btn-sm" onclick="retryFile({{ $cartFile->id }})" title="Yeniden Dene">
                                                            <i class="fas fa-redo"></i>
                                                        </button>
                                                    @endif
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteFile({{ $cartFile->id }})" title="Sil">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-3">
                            {{ $cartFiles->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Cart dosyası bulunamadı</h5>
                            <p class="text-muted">Henüz hiç dosya yüklenmemiş.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- File Details Modal -->
<div class="modal fade" id="fileDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Dosya Detayları</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="fileDetailsContent">
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
    // Cart detaylarını göstermek için modal açabilirsiniz
    Swal.fire({
        title: 'Cart Detayları',
        text: `Cart ID: ${cartId}`,
        icon: 'info'
    });
}
</script>
@endsection 