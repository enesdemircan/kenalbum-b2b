@extends('admin.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <h1 class="h4 mb-3">Özelleştirme Kategorisi Düzenle</h1>
        <form action="{{ route('admin.customization-categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="title" class="form-label">Başlık</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ $category->title }}" required>
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Tip</label>
                <select name="type" id="type" class="form-select" required>
                    <option value="select" @if($category->type == 'select') selected @endif>Select</option>
                    <option value="input" @if($category->type == 'input') selected @endif>Input</option>
                    <option value="checkbox" @if($category->type == 'checkbox') selected @endif>Checkbox</option>
                    <option value="radio" @if($category->type == 'radio') selected @endif>Radio</option>
                    <option value="file" @if($category->type == 'file') selected @endif>File</option>
                    <option value="files" @if($category->type == 'files') selected @endif>Files</option>
                    <option value="hidden" @if($category->type == 'hidden') selected @endif>Hidden</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="ust_id" class="form-label">Üst Kategori</label>
                <select name="ust_id" id="ust_id" class="form-select">
                    <option value="0">Yok (Ana Kategori)</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @if($category->ust_id == $cat->id) selected @endif>{{ $cat->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="order" class="form-label">Sıra</label>
                <input type="number" name="order" id="order" class="form-control" value="{{ $category->order }}">
            </div>
            <div class="mb-3">
                <label for="required" class="form-label">Zorunlu mu?</label>
                <select name="required" id="required" class="form-select">
                    <option value="1" @if($category->required) selected @endif>Evet</option>
                    <option value="0" @if(!$category->required) selected @endif>Hayır</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Güncelle</button>
            <a href="{{ route('admin.customization-categories.index') }}" class="btn btn-secondary">Geri</a>
        </form>
    </div>
</div>
@endsection 