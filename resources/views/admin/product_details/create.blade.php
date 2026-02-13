@extends('admin.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3">Yeni Detay Ekle - {{ $product->title }}</h1>
            <a href="{{ route('admin.product-details.index', $product->id) }}" class="btn btn-secondary">
                ← Detaylara Dön
            </a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-body">
                <form action="{{ route('admin.product-details.store', $product->id) }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Başlık <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title" 
                               value="{{ old('title') }}" 
                               placeholder="Detay başlığını girin"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="text" class="form-label">İçerik <span class="text-danger">*</span></label>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Resim Yükleme Kuralları:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Sadece PNG ve JPG formatında resim yükleyebilirsiniz</li>
                                <li>Maksimum dosya boyutu: 5MB</li>
                                <li>Resimler otomatik olarak sıkıştırılacak ve optimize edilecektir</li>
                            </ul>
                        </div>
                        <textarea class="form-control @error('text') is-invalid @enderror" 
                                  id="text" 
                                  name="text" 
                                  rows="15" 
                                  placeholder="Detay içeriğini girin"
                                  required>{{ old('text') }}</textarea>
                        @error('text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.product-details.index', $product->id) }}" class="btn btn-secondary">
                            İptal
                        </a>
                        <button type="submit" class="btn btn-primary" onclick="syncEditorContent()">
                            <i class="bi bi-check-circle"></i> Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- CKEditor CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>

<script>
            ClassicEditor
            .create(document.querySelector('#text'), {
                toolbar: {
                    items: [
                        'undo', 'redo',
                        '|', 'heading',
                        '|', 'bold', 'italic', 'underline', 'strikethrough',
                        '|', 'fontColor', 'fontBackgroundColor',
                        '|', 'link', 'blockQuote', 'insertTable', 'mediaEmbed', 'imageUpload',
                        '|', 'bulletedList', 'numberedList',
                        '|', 'outdent', 'indent',
                        '|', 'alignment',
                        '|', 'horizontalLine', 'specialCharacters',
                        '|', 'removeFormat'
                    ]
                },
                language: 'tr',
                placeholder: 'Detay içeriğini buraya yazın...',
                height: '400px'
            })
            .then(editor => {
                // Form validation için CKEditor içeriğini textarea'ya senkronize et
                editor.model.document.on('change:data', () => {
                    const data = editor.getData();
                    document.querySelector('#text').value = data;
                });
                
                // Resim upload adapter'ını manuel olarak ekle
                editor.plugins.get('FileRepository').createUploadAdapter = function(loader) {
                    console.log('Upload adapter created');
                    return {
                        upload: function() {
                            console.log('Upload function called');
                            return new Promise(function(resolve, reject) {
                                console.log('Promise created');
                                loader.file.then(function(file) {
                                    console.log('File selected:', file);
                                    console.log('File name:', file.name);
                                    console.log('File size:', file.size);
                                    console.log('File type:', file.type);
                                    
                                    var formData = new FormData();
                                    formData.append('upload', file);
                                    
                                    console.log('FormData created, sending to:', '{{ route("admin.upload-image") }}');
                                    
                                    fetch('{{ route("admin.upload-image") }}', {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: formData
                                    })
                                    .then(response => {
                                        console.log('Response status:', response.status);
                                        console.log('Response headers:', response.headers);
                                        return response.json();
                                    })
                                    .then(data => {
                                        console.log('Response data:', data);
                                        if (data.uploaded) {
                                            console.log('Upload successful, resolving with URL:', data.url);
                                            resolve({
                                                default: data.url
                                            });
                                        } else {
                                            console.log('Upload failed:', data.error.message);
                                            // Kullanıcıya daha açıklayıcı hata mesajı göster
                                            let errorMessage = data.error.message;
                                            if (errorMessage.includes('mimes')) {
                                                errorMessage = 'Sadece PNG ve JPG formatında resim yükleyebilirsiniz.';
                                            } else if (errorMessage.includes('max')) {
                                                errorMessage = 'Resim boyutu 5MB\'dan büyük olamaz.';
                                            }
                                            reject(errorMessage);
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Upload error:', error);
                                        reject('Resim yüklenirken hata oluştu: ' + error);
                                    });
                                }).catch(function(error) {
                                    console.error('File loading error:', error);
                                    reject('Dosya yüklenirken hata oluştu: ' + error);
                                });
                            });
                        }
                    };
                };
                
                console.log('CKEditor başarıyla yüklendi!');
                
                // Global editor değişkeni
                window.editor = editor;
            })
            .catch(error => {
                console.error('CKEditor yüklenirken hata oluştu:', error);
            });
            
        // Form submit öncesi CKEditor içeriğini senkronize et
        function syncEditorContent() {
            if (window.editor) {
                const data = window.editor.getData();
                document.querySelector('#text').value = data;
            }
        }
</script>

<style>
    .ck-editor__editable {
        min-height: 300px;
    }
    
    .ck-editor__editable_inline {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }
    
    .ck-editor__editable_inline:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>

@endsection 