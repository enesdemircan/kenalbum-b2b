@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1 class="page-title">S3 Dosyaları</h1>
        <p class="page-subtitle">S3 depolama dosyalarını yönetin</p>
    </div>
    <div class="d-flex gap-2">
        @if($currentPath)
            <a href="{{ route('admin.s3-files.index') }}" class="btn-material btn-material-secondary">
                <span class="material-icons">home</span>
                Ana Klasör
            </a>
        @endif
        <button type="button" class="btn-material btn-material-danger" onclick="clearAllFiles()">
            <span class="material-icons">delete_sweep</span>
            Tümünü Temizle
        </button>
    </div>
</div>

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

<div class="row mb-4">
    <div class="col-md-6">
        <div class="material-card-outlined p-3">
            <div class="d-flex align-items-center gap-3">
                <span class="material-icons" style="font-size: 48px; color: var(--md-info);">description</span>
                <div>
                    <div class="text-muted small">Toplam Dosya</div>
                    <div class="h4 mb-0">{{ $fileCount }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="material-card-outlined p-3">
            <div class="d-flex align-items-center gap-3">
                <span class="material-icons" style="font-size: 48px; color: var(--md-warning);">storage</span>
                <div>
                    <div class="text-muted small">Toplam Boyut</div>
                    <div class="h4 mb-0">{{ number_format($totalSize / 1024 / 1024, 2) }} MB</div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(count($folders) > 0)
    <div class="material-card-elevated mb-4">
        <div class="material-card-header">
            <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">folder</span>Klasörler</h5>
        </div>
        <div class="material-card-body p-0">
            <div class="material-table-wrapper">
                <table class="material-table">
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
                                        <span class="material-icons text-warning me-2">folder</span>
                                        <div>
                                            <div class="fw-bold">{{ $folder['name'] }}</div>
                                            <small class="text-muted">{{ $folder['path'] }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="material-badge material-badge-info">{{ $folder['file_count'] }}</span></td>
                                <td><span class="material-badge material-badge-secondary">{{ $folder['size_formatted'] }}</span></td>
                                <td>
                                    <a href="{{ route('admin.s3-files.index', ['path' => $folder['path']]) }}" class="btn-material-icon btn-material-icon-primary" title="Aç">
                                        <span class="material-icons">folder_open</span>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

@if(count($files) > 0)
    <div class="material-card-elevated mb-4">
        <div class="material-card-header">
            <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">insert_drive_file</span>Dosyalar</h5>
        </div>
        <div class="material-card-body p-0">
            <div class="material-table-wrapper">
                <table class="material-table">
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
                                        <span class="material-icons text-muted me-2">description</span>
                                        <div>
                                            <div class="fw-bold">{{ $file['name'] }}</div>
                                            <small class="text-muted">{{ $file['path'] }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="material-badge material-badge-secondary">{{ $file['size_formatted'] }}</span></td>
                                <td><small>{{ $file['last_modified'] }}</small></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ $file['url'] }}" target="_blank" class="btn-material-icon btn-material-icon-info" title="Görüntüle">
                                            <span class="material-icons">visibility</span>
                                        </a>
                                        <button type="button" class="btn-material-icon btn-material-icon-danger" onclick="deleteFile('{{ $file['path'] }}')" title="Sil">
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

    <div class="mb-3">
        <button type="button" class="btn-material btn-material-warning" onclick="deleteSelectedFiles()">
            <span class="material-icons">delete</span>
            Seçilenleri Sil
        </button>
    </div>
@endif

@if(count($folders) == 0 && count($files) == 0)
    <div class="material-card-elevated">
        <div class="material-card-body text-center py-5">
            <span class="material-icons" style="font-size: 80px; color: var(--md-text-disabled);">folder_open</span>
            <h5 class="text-muted mt-3">
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
    </div>
@endif

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
