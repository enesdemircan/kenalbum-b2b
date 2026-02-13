@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="page-title">Yeni Özelleştirme Kategorisi Ekle</h1>
        <p class="page-subtitle">Kategori bilgilerini girin</p>
    </div>
    <a href="{{ route('admin.customization-categories.index') }}" class="btn-material btn-material-secondary">
        <span class="material-icons">arrow_back</span> Geri Dön
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="material-card-elevated">
            <div class="material-card-body">
                <form action="{{ route('admin.customization-categories.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Başlık</label>
                        <input type="text" name="title" id="title" class="form-control form-control-material" required>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Tip</label>
                        <select name="type" id="type" class="form-select form-control-material" required>
                            <option value="select">Select</option>
                            <option value="input">Input</option>
                            <option value="checkbox">Checkbox</option>
                            <option value="radio">Radio</option>
                            <option value="file">File</option>
                            <option value="files">Files</option>
                            <option value="hidden">Hidden</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="ust_id" class="form-label">Üst Kategori</label>
                        <select name="ust_id" id="ust_id" class="form-select form-control-material">
                            <option value="0">Yok (Ana Kategori)</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="order" class="form-label">Sıra</label>
                        <input type="number" name="order" id="order" class="form-control form-control-material" value="0">
                    </div>
                    <div class="mb-3">
                        <label for="required" class="form-label">Zorunlu mu?</label>
                        <select name="required" id="required" class="form-select form-control-material">
                            <option value="1">Evet</option>
                            <option value="0">Hayır</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="submit" class="btn-material btn-material-success">
                            <span class="material-icons">save</span> Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
