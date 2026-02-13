@extends('admin.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <h1 class="h4 mb-3">Yeni Özelleştirme Kategorisi Ekle</h1>
        <form action="{{ route('admin.customization-categories.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="title" class="form-label">Başlık</label>
                <input type="text" name="title" id="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Tip</label>
                <select name="type" id="type" class="form-select" required>
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
                <select name="ust_id" id="ust_id" class="form-select">
                    <option value="0">Yok (Ana Kategori)</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="order" class="form-label">Sıra</label>
                <input type="number" name="order" id="order" class="form-control" value="0">
            </div>
            <div class="mb-3">
                <label for="required" class="form-label">Zorunlu mu?</label>
                <select name="required" id="required" class="form-select">
                    <option value="1">Evet</option>
                    <option value="0">Hayır</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Kaydet</button>
            <a href="{{ route('admin.customization-categories.index') }}" class="btn btn-secondary">Geri</a>
        </form>
    </div>
</div>
@endsection 