@extends('admin.layout')

@section('content')
<div class="page-header mb-4">
    <h1 class="page-title">Sipariş Durumu Düzenle</h1>
    <p class="page-subtitle">{{ $orderStatus->title }} - Durum bilgilerini güncelleyin</p>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="material-card-elevated">
            <div class="material-card-header">
                <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">edit</span>Durum Bilgilerini Düzenle</h5>
            </div>
            <div class="material-card-body">
                <form action="{{ route('admin.order-statuses.update', $orderStatus->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Durum Adı *</label>
                        <input type="text" 
                               name="title" 
                               id="title" 
                               class="form-control form-control-material @error('title') is-invalid @enderror" 
                               value="{{ old('title', $orderStatus->title) }}" 
                               required 
                               maxlength="255"
                               placeholder="Örn: Onay Bekliyor">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Roller</label>
                        <div class="row">
                            @foreach($roles as $role)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               name="roles[]" 
                                               value="{{ $role->id }}" 
                                               id="role_{{ $role->id }}"
                                               {{ in_array($role->id, old('roles', $orderStatus->roles->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="role_{{ $role->id }}">
                                            {{ $role->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('roles')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.order-statuses.index') }}" class="btn-material btn-material-secondary">
                            <span class="material-icons">close</span>
                            İptal
                        </a>
                        <button type="submit" class="btn-material btn-material-warning">
                            <span class="material-icons">save</span>
                            Güncelle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
