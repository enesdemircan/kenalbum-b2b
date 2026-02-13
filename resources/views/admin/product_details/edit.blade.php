@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Detay Düzenle</h1>
        <p class="page-subtitle">{{ $product->title }} - {{ $detail->title }}</p>
    </div>
    <a href="{{ route('admin.product-details.index', $product->id) }}" class="btn-material btn-material-secondary">
        <span class="material-icons">arrow_back</span> Detaylara Dön
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        @if($errors->any())
            <div class="material-alert material-alert-danger alert-dismissible fade show mb-3" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="material-card-elevated">
            <div class="material-card-body">
                <form action="{{ route('admin.product-details.update', [$product->id, $detail->id]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Başlık <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control form-control-material @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title" 
                               value="{{ old('title', $detail->title) }}" 
                               placeholder="Detay başlığını girin"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="text" class="form-label">İçerik <span class="text-danger">*</span></label>
                        <div class="material-alert material-alert-info mb-2">
                            <span class="material-icons">info</span>
                            <strong>Resim Yükleme Kuralları:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Sadece PNG ve JPG formatında resim yükleyebilirsiniz</li>
                                <li>Maksimum dosya boyutu: 5MB</li>
                                <li>Resimler otomatik olarak sıkıştırılacak ve optimize edilecektir</li>
                            </ul>
                        </div>
                        <textarea class="form-control form-control-material @error('text') is-invalid @enderror" 
                                  id="text" 
                                  name="text" 
                                  rows="15" 
                                  placeholder="Detay içeriğini girin"
                                  required>{{ old('text', $detail->text) }}</textarea>
                        @error('text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn-material btn-material-primary" onclick="syncEditorContent()">
                            <span class="material-icons">save</span> Güncelle
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
                return {
                    upload: function() {
                        return new Promise(function(resolve, reject) {
                            loader.file.then(function(file) {
                                console.log('File selected:', file);
                                
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
                                    return response.json();
                                })
                                .then(data => {
                                    console.log('Response data:', data);
                                    if (data.uploaded) {
                                        resolve({
                                            default: data.url
                                        });
                                    } else {
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