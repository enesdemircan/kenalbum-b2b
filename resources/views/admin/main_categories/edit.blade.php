@extends('admin.layout')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <h1 class="h4 mb-3">Ana Kategori Düzenle</h1>
        <form action="{{ route('admin.main-categories.update', $mainCategory) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="title" class="form-label">Başlık</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ $mainCategory->title }}" required>
            </div>
            <div class="mb-3">
                <label for="slug" class="form-label">Slug (URL)</label>
                <input type="text" name="slug" id="slug" class="form-control" value="{{ $mainCategory->slug }}" placeholder="Boş bırakılırsa otomatik oluşturulur">
                <div class="form-text">Örnek: "Albüm Kategorisi" → "album-kategorisi"</div>
            </div>
            <div class="mb-3">
                <label for="ust_id" class="form-label">Üst Kategori</label>
                <select name="ust_id" id="ust_id" class="form-select">
                    <option value="0">Yok (Ana Kategori)</option>
                    @foreach($mainCategories as $cat)
                        <option value="{{ $cat->id }}" @if($mainCategory->ust_id == $cat->id) selected @endif>{{ $cat->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label for="order" class="form-label">Sıra</label>
                <input type="number" name="order" id="order" class="form-control" value="{{ $mainCategory->order }}">
            </div>
            <button type="submit" class="btn btn-success">Güncelle</button>
            <a href="{{ route('admin.main-categories.index') }}" class="btn btn-secondary">Geri</a>
        </form>
    </div>
</div>
@endsection 