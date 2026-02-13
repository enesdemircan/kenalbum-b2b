@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">İndirim Grupları</h1>
    <a href="{{ route('admin.discount-groups.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Yeni İndirim Grubu
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<!-- Filtre Formu -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Filtrele</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.discount-groups.index') }}" class="row g-3">
            <div class="col-md-6">
                <label for="name" class="form-label">Kampanya Adı</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $filters['name'] ?? '' }}" placeholder="Kampanya adı ara...">
            </div>
            <div class="col-md-6 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search"></i> Filtrele
                </button>
                <a href="{{ route('admin.discount-groups.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Temizle
                </a>
            </div>
        </form>
    </div>
</div>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
        
            <th>Ad</th>
            <th>Açıklama</th>
            <th>İndirim %</th>
            <th>Ana Kategori</th>
            <th>Firma</th>
            <th>Durum</th>
            <th>Tarih Aralığı</th>
            <th style="width: 190px;"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($discountGroups as $group)
        <tr>
          
            <td>{{ $group->name }}</td>
            <td>{{ Str::limit($group->description, 50) }}</td>
            <td>%{{ $group->discount_percentage }}</td>
            <td>{{ $group->mainCategory->title }}</td>
            <td>
                @if($group->customers->count() > 0)
                    @foreach($group->customers as $customer)
                        <span class="badge bg-primary">{{ $customer->unvan }}</span>
                    @endforeach
                @else
                    <span class="text-muted">Firma Yok</span>
                @endif
            </td>
            <td>
                @if($group->is_active)
                    <span class="badge bg-success">Aktif</span>
                @else
                    <span class="badge bg-danger">Pasif</span>
                @endif
            </td>
            <td>
                @if($group->start_date && $group->end_date)
                    {{ $group->start_date->format('d.m.Y') }} - {{ $group->end_date->format('d.m.Y') }}
                @elseif($group->start_date)
                    {{ $group->start_date->format('d.m.Y') }} - Süresiz
                @elseif($group->end_date)
                    Süresiz - {{ $group->end_date->format('d.m.Y') }}
                @else
                    Süresiz
                @endif
            </td>
            <td>
                <a href="{{ route('admin.discount-groups.show', $group) }}" class="btn btn-sm btn-info" title="Görüntüle">
                    <i class="bi bi-eye"></i>
                </a>
                <a href="{{ route('admin.discount-groups.edit', $group) }}" class="btn btn-sm btn-warning" title="Düzenle">
                    <i class="bi bi-pencil-square"></i>
                </a>
                <form action="{{ route('admin.discount-groups.destroy', $group) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bu indirim grubunu silmek istediğinizden emin misiniz?')" title="Sil">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Sayfalama -->
<div class="d-flex justify-content-center mt-4">
    {{ $discountGroups->appends(request()->query())->links() }}
</div>

@endsection 