@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">Firmalar</h1>
        <p class="page-subtitle">Tüm firmaları görüntüleyin ve yönetin</p>
    </div>
    <a href="{{ route('admin.customers.create') }}" class="btn-material btn-material-primary">
        <span class="material-icons">add</span>
        Yeni Firma Ekle
    </a>
</div>

@if(session('success'))
    <div class="material-alert material-alert-success">
        <span class="material-icons">check_circle</span>
        <span>{{ session('success') }}</span>
    </div>
@endif

<!-- Filtre Accordion -->
<div class="filter-accordion material-card">
    <div class="filter-header" id="filterHeader" onclick="toggleFilterAccordion()">
        <span class="material-icons">filter_list</span>
        <span>Filtrele</span>
        <span class="material-icons expand-icon">expand_more</span>
    </div>
    <div class="filter-body" id="filterBody">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="unvan" class="form-label">Ünvan</label>
                <input type="text" class="form-control form-control-material" id="unvan" name="unvan" value="{{ $filters['unvan'] ?? '' }}" placeholder="Ünvan ara...">
            </div>
            <div class="col-md-4">
                <label for="phone" class="form-label">Telefon</label>
                <input type="text" class="form-control form-control-material" id="phone" name="phone" value="{{ $filters['phone'] ?? '' }}" placeholder="Telefon ara...">
            </div>
            <div class="col-md-4">
                <label for="email" class="form-label">E-posta</label>
                <input type="text" class="form-control form-control-material" id="email" name="email" value="{{ $filters['email'] ?? '' }}" placeholder="E-posta ara...">
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <button type="submit" class="btn-material btn-material-primary">
                    <span class="material-icons">search</span>
                    Filtrele
                </button>
                <a href="{{ route('admin.customers.index') }}" class="btn-material btn-material-secondary">
                    <span class="material-icons">clear</span>
                    Temizle
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Firmalar Tablosu -->
<div class="material-table-wrapper">
    <table class="material-table">
        <thead>
            <tr>
                <th>Firma ID</th>
                <th>Ünvan</th>
                <th>Telefon</th>
                <th>E-posta</th>
                <th>Bakiye</th>
                <th>Vergi Dairesi</th>
                <th>Vergi No</th>
                <th style="width: 220px">İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $customer)
                <tr>
                    <td><code style="background: #f5f5f5; padding: 4px 8px; border-radius: 4px; font-size: 12px">{{ $customer->firma_id }}</code></td>
                    <td>{{ $customer->unvan }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>
                        <span class="material-badge material-badge-{{ $customer->balance > 0 ? 'success' : ($customer->balance < 0 ? 'danger' : 'secondary') }}">
                            {{ $customer->formatted_balance }}
                        </span>
                    </td>
                    <td>{{ $customer->vergi_dairesi }}</td>
                    <td>{{ $customer->vergi_numarasi }}</td>
                    <td>
                        <div class="d-flex gap-1 justify-content-end">
                            <button class="btn-material-icon btn-material-icon-info" title="Görüntüle" onclick="window.location.href='{{ route('admin.customers.show', $customer->id) }}'">
                                <span class="material-icons">visibility</span>
                            </button>
                            <button class="btn-material-icon btn-material-icon-warning" title="Düzenle" onclick="window.location.href='{{ route('admin.customers.edit', $customer->id) }}'">
                                <span class="material-icons">edit</span>
                            </button>
                            <button class="btn-material-icon btn-material-icon-success" title="Tahsilat" onclick="window.location.href='{{ route('admin.customers.collections', $customer->id) }}'">
                                <span class="material-icons">payments</span>
                            </button>
                            <form action="{{ route('admin.impersonate.start', $customer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ $customer->unvan }} adına giriş yapılacak. Devam etmek istiyor musunuz?')">
                                @csrf
                                <button type="submit" class="btn-material-icon btn-material-icon-primary" title="Firma adına giriş yap">
                                    <span class="material-icons">login</span>
                                </button>
                            </form>
                            <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu müşteriyi silmek istediğinizden emin misiniz?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-material-icon btn-material-icon-danger" title="Sil">
                                    <span class="material-icons">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Sayfalama -->
<div class="material-pagination">
    {{ $customers->appends(request()->query())->links() }}
</div>

<script>
    // Filter Accordion Toggle
    function toggleFilterAccordion() {
        const header = document.getElementById('filterHeader');
        const body = document.getElementById('filterBody');
        const isOpen = body.classList.contains('open');
        
        if (isOpen) {
            body.classList.remove('open');
            header.classList.remove('active');
            localStorage.setItem('customersFilterOpen', 'false');
        } else {
            body.classList.add('open');
            header.classList.add('active');
            localStorage.setItem('customersFilterOpen', 'true');
        }
    }
    
    // Remember filter state
    document.addEventListener('DOMContentLoaded', function() {
        const filterOpen = localStorage.getItem('customersFilterOpen');
        if (filterOpen === 'true') {
            document.getElementById('filterBody').classList.add('open');
            document.getElementById('filterHeader').classList.add('active');
        }
    });
</script>

@endsection
