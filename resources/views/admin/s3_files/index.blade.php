@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">S3 Dosyaları</h3>
                    <div class="card-tools">
                        @if($currentPath)
                            <a href="{{ route('admin.s3-files.index') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-home"></i> Ana Klasör
                            </a>
                        @endif
                        <button type="button" class="btn btn-danger" onclick="clearAllFiles()">
                            <i class="fas fa-trash"></i> Tümünü Temizle
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($currentPath)
                        <nav aria-label="breadcrumb" class="mb-3">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('admin.s3-files.index') }}">Ana Klasör</a>
                                </li>
                                @php
                                    $pathParts = explode('/', $currentPath);
                                    $currentPathPart = '';
                                @endphp
                                @foreach($pathParts as $part)
                                    @php
                                        $currentPathPart .= ($currentPathPart ? '/' : '') . $part;
                                    @endphp
                                    <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
                                        @if(!$loop->last)
                                            <a href="{{ route('admin.s3-files.index', ['path' => $currentPathPart]) }}">{{ $part }}</a>
                                        @else
                                            {{ $part }}
                                        @endif
                                    </li>
                                @endforeach
                            </ol>
                        </nav>
                    @endif
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-file"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Toplam Dosya</span>
                                    <span class="info-box-number">{{ $fileCount }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-hdd"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Toplam Boyut</span>
                                    <span class="info-box-number">{{ number_format($totalSize / 1024 / 1024, 2) }} MB</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(count($folders) > 0)
                        <div class="mb-4">
                            <h5><i class="fas fa-folder text-warning"></i> Klasörler</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Klasör Adı</th>
                                            <th style="width: 120px;">Dosya Sayısı</th>
                                            <th style="width: 120px;">Toplam Boyut</th>
                                            <th style="width: 100px;">İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($folders as $folder)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-folder text-warning me-2"></i>
                                                        <div>
                                                            <div class="fw-bold">{{ $folder['name'] }}</div>
                                                            <small class="text-muted">{{ $folder['path'] }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $folder['file_count'] }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $folder['size_formatted'] }}</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.s3-files.index', ['path' => $folder['path']]) }}" class="btn btn-primary btn-sm" title="Aç">
                                                        <i class="fas fa-folder-open"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if(count($files) > 0)
                        <div class="mb-4">
                            <h5><i class="fas fa-file text-info"></i> Dosyalar</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 50px;">
                                                <input type="checkbox" id="select-all" onchange="toggleSelectAll()">
                                            </th>
                                            <th>Dosya Adı</th>
                                            <th style="width: 120px;">Boyut</th>
                                            <th style="width: 150px;">Son Değişiklik</th>
                                            <th style="width: 100px;">İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($files as $file)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="file-checkbox" value="{{ $file['path'] }}">
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-file me-2 text-muted"></i>
                                                        <div>
                                                            <div class="fw-bold">{{ $file['name'] }}</div>
                                                            <small class="text-muted">{{ $file['path'] }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $file['size_formatted'] }}</span>
                                                </td>
                                                <td>
                                                    <small>{{ $file['last_modified'] }}</small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ $file['url'] }}" target="_blank" class="btn btn-info btn-sm" title="Görüntüle">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteFile('{{ $file['path'] }}')" title="Sil">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                        <div class="mt-3">
                            <button type="button" class="btn btn-warning" onclick="deleteSelectedFiles()">
                                <i class="fas fa-trash"></i> Seçilenleri Sil
                            </button>
                        </div>
                    @if(count($folders) == 0 && count($files) == 0)
                        <div class="text-center py-5">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">
                                @if($currentPath)
                                    Bu klasörde dosya bulunamadı
                                @else
                                    S3'te dosya bulunamadı
                                @endif
                            </h5>
                            <p class="text-muted">
                                @if($currentPath)
                                    Bu klasör boş veya mevcut değil.
                                @else
                                    Henüz hiç dosya yüklenmemiş.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function deleteFile(filePath) {
    Swal.fire({
        title: 'Emin misiniz?',
        text: `"${filePath}" dosyasını silmek istediğinizden emin misiniz?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Evet, Sil!',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route("admin.s3-files.delete") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    file_path: filePath
                })
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

function deleteSelectedFiles() {
    const selectedFiles = Array.from(document.querySelectorAll('.file-checkbox:checked')).map(cb => cb.value);
    
    if (selectedFiles.length === 0) {
        Swal.fire('Uyarı', 'Lütfen silinecek dosyaları seçin.', 'warning');
        return;
    }

    Swal.fire({
        title: 'Emin misiniz?',
        text: `${selectedFiles.length} dosyayı silmek istediğinizden emin misiniz?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Evet, Sil!',
        cancelButtonText: 'İptal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route("admin.s3-files.delete-multiple") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    file_paths: selectedFiles
                })
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

function clearAllFiles() {
    Swal.fire({
        title: 'DİKKAT!',
        text: 'S3\'teki TÜM dosyaları silmek istediğinizden emin misiniz? Bu işlem geri alınamaz!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'EVET, TÜMÜNÜ SİL!',
        cancelButtonText: 'İptal',
        input: 'text',
        inputLabel: 'Onay için "TEMIZLE" yazın',
        inputPlaceholder: 'TEMIZLE',
        inputValidator: (value) => {
            if (value !== 'TEMIZLE') {
                return 'Onay için "TEMIZLE" yazmanız gerekiyor!';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('{{ route("admin.s3-files.clear-all") }}', {
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

function toggleSelectAll() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.file-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}
</script>
@endsection 