@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Firmalar</h1>
    <a href="{{ route('admin.customers.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Yeni Firma Ekle
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<!-- Filtre Formu -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Filtrele</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="unvan" class="form-label">Ünvan</label>
                <input type="text" class="form-control" id="unvan" name="unvan" value="{{ $filters['unvan'] ?? '' }}" placeholder="Ünvan ara...">
            </div>
            <div class="col-md-4">
                <label for="phone" class="form-label">Telefon</label>
                <input type="text" class="form-control" id="phone" name="phone" value="{{ $filters['phone'] ?? '' }}" placeholder="Telefon ara...">
            </div>
            <div class="col-md-4">
                <label for="email" class="form-label">E-posta</label>
                <input type="text" class="form-control" id="email" name="email" value="{{ $filters['email'] ?? '' }}" placeholder="E-posta ara...">
            </div>
            <div class="col-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search"></i> Filtrele
                </button>
                <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Temizle
                </a>
            </div>
        </form>
    </div>
</div>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
          
            <th>Firma ID</th>
            <th>Ünvan</th>
            <th>Telefon</th>
            <th>E-posta</th>
            <th>Bakiye</th>
            <th>Vergi Dairesi</th>
            <th>Vergi No</th>
            <th style="width: 190px;"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($customers as $customer)
            <tr>
                
                <td><code>{{ $customer->firma_id }}</code></td>
                <td>{{ $customer->unvan }}</td>
                <td>{{ $customer->phone }}</td>
                <td>{{ $customer->email }}</td>
                <td>
                    <span class="badge {{ $customer->balance > 0 ? 'bg-success' : ($customer->balance < 0 ? 'bg-danger' : 'bg-secondary') }}">
                        {{ $customer->formatted_balance }}
                    </span>
                </td>
                <td>{{ $customer->vergi_dairesi }}</td>
                <td>{{ $customer->vergi_numarasi }}</td>
                <td>
                    <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn btn-sm btn-info" title="Görüntüle">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-sm btn-warning" title="Düzenle">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <a href="{{ route('admin.customers.collections', $customer->id) }}" class="btn btn-sm btn-success" title="Tahsilat İşlemleri">
                        <i class="bi bi-cash-coin"></i>
                    </a>
                    <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bu müşteriyi silmek istediğinizden emin misiniz?')" title="Sil">
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
    {{ $customers->appends(request()->query())->links() }}
</div>

@endsection 