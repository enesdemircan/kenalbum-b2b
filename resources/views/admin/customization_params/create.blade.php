@extends('admin.layout')

@section('content')
    <h1>{{ $category->title }} - Yeni Parametre Ekle</h1>
    <form action="{{ route('admin.customization-params.store', $category->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="key" class="form-label">Parametre Adı</label>
            <input type="text" name="key" id="key" class="form-control" required value="{{ old('key') }}">
        </div>
        
        <div class="mb-3">
            <label for="option2" class="form-label">Dosya Yüklemesi Yapılsın mı?</label>
            <select name="option2" id="option2" class="form-control">
                <option value="false" {{ old('option2', 'false') == 'false' ? 'selected' : '' }}>Hayır</option>
                <option value="true" {{ old('option2', 'false') == 'true' ? 'selected' : '' }}>Evet</option>
            </select>
            <small class="text-muted">Bu parametre için dosya yükleme alanı gösterilsin mi?</small>
        </div>
        
        <div class="mb-3" id="text_value_section" style="display: none;">
            <label for="value" class="form-label">Değer (Metin)</label>
            <input type="text" name="value" id="value" class="form-control" value="{{ old('value') }}">
        </div>
        
        <div class="mb-3" id="file_upload_section" style="display: none;">
            <label for="value_file" class="form-label">Değer (Resim Yükle)</label>
            <input type="file" name="value_file" id="value_file" class="form-control" accept="image/*">
            <small class="text-muted">Yüklenen resmin path'i otomatik olarak value alanına yazılacaktır.</small>
            <div id="image_preview" class="mt-2" style="display: none;">
                <img id="preview_img" src="" alt="Önizleme" style="max-width: 200px; max-height: 200px; border-radius: 8px;">
            </div>
        </div>
        
        <div class="mb-3">
            <label for="order" class="form-label">Sıra</label>
            <input type="number" name="order" id="order" class="form-control" value="{{ old('order', 0) }}">
        </div>
        
        <button type="submit" class="btn btn-success">Kaydet</button>
        <a href="{{ route('admin.customization-params.index', $category->id) }}" class="btn btn-secondary">Geri</a>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const option2Select = document.getElementById('option2');
            const fileUploadSection = document.getElementById('file_upload_section');
            
            // Sayfa yüklendiğinde mevcut seçimi kontrol et
            toggleFileSection();
            
            // Select değiştiğinde file bölümünü göster/gizle
            option2Select.addEventListener('change', function() {
                toggleFileSection();
            });
            
            function toggleFileSection() {
                if (option2Select.value === 'true') {
                    // Dosya yükleme seçildi - file alanını göster
                    fileUploadSection.style.display = 'block';
                } else {
                    // Dosya yükleme seçilmedi - file alanını gizle
                    fileUploadSection.style.display = 'none';
                }
            }
        });
    </script>
@endsection 