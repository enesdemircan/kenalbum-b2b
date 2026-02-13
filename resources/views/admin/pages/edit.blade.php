@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Sayfa Düzenle</h1>
        <p class="page-subtitle">{{ $page->title }}</p>
    </div>
    <a href="{{ route('admin.pages.index') }}" class="btn-material btn-material-secondary">
        <span class="material-icons">arrow_back</span> Geri Dön
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        @if($errors->any())
            <div class="material-alert material-alert-danger mb-3">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="material-card-elevated">
            <div class="material-card-body">
                <form action="{{ route('admin.pages.update', $page) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Başlık *</label>
                        <input type="text" name="title" id="title" class="form-control form-control-material @error('title') is-invalid @enderror" value="{{ old('title', $page->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label">Slug</label>
                        <input type="text" name="slug" id="slug" class="form-control form-control-material @error('slug') is-invalid @enderror" value="{{ old('slug', $page->slug) }}" placeholder="Boş bırakırsanız başlıktan otomatik oluşturulur">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">URL'de kullanılacak kısa isim. Boş bırakırsanız başlıktan otomatik oluşturulur.</small>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Açıklama</label>
                        <textarea name="description" id="description" class="form-control form-control-material @error('description') is-invalid @enderror" rows="3">{{ old('description', $page->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="text" class="form-label">İçerik <span class="text-danger">*</span></label>
                        <textarea name="text" id="text" class="form-control form-control-material @error('text') is-invalid @enderror" rows="15" placeholder="Sayfa içeriğini girin" required>{{ old('text', $page->text) }}</textarea>
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

@push('scripts')
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
        placeholder: 'Sayfa içeriğini buraya yazın...',
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
                                    reject(data.error.message);
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
@endpush
@endsection 