@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">İndirim Grupları</h1>
        <p class="page-subtitle">Kampanyaları ve indirim gruplarını yönetin</p>
    </div>
    <a href="{{ route('admin.discount-groups.create') }}" class="btn-material btn-material-primary">
        <span class="material-icons">add</span>
        Yeni İndirim Grubu
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
        <form method="GET" action="{{ route('admin.discount-groups.index') }}" class="row g-3">
            <div class="col-md-6">
                <label for="name" class="form-label">Kampanya Adı</label>
                <input type="text" class="form-control form-control-material" id="name" name="name" value="{{ $filters['name'] ?? '' }}" placeholder="Kampanya adı ara...">
            </div>
            <div class="col-md-6 d-flex align-items-end justify-content-end gap-2">
                <button type="submit" class="btn-material btn-material-primary">
                    <span class="material-icons">search</span>
                    Filtrele
                </button>
                <a href="{{ route('admin.discount-groups.index') }}" class="btn-material btn-material-secondary">
                    <span class="material-icons">clear</span>
                    Temizle
                </a>
            </div>
        </form>
    </div>
</div>

<!-- İndirim Grupları Tablosu -->
<div class="material-table-wrapper">
    <table class="material-table">
        <thead>
            <tr>
                <th>Ad</th>
                <th>Açıklama</th>
                <th>İndirim %</th>
                <th>Ana Kategori</th>
                <th>Firma</th>
                <th>Durum</th>
                <th>Tarih Aralığı</th>
                <th style="width: 190px">İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @foreach($discountGroups as $group)
            <tr>
                <td><strong>{{ $group->name }}</strong></td>
                <td>{{ Str::limit($group->description, 50) }}</td>
                <td><span class="material-badge material-badge-warning">%{{ $group->discount_percentage }}</span></td>
                <td>{{ $group->mainCategory->title }}</td>
                <td>
                    @if($group->customers->count() > 0)
                        @foreach($group->customers as $customer)
                            <span class="material-badge material-badge-primary">{{ $customer->unvan }}</span>
                        @endforeach
                    @else
                        <span class="text-muted">Firma Yok</span>
                    @endif
                </td>
                <td>
                    @if($group->is_active)
                        <span class="material-badge material-badge-success">Aktif</span>
                    @else
                        <span class="material-badge material-badge-danger">Pasif</span>
                    @endif
                </td>
                <td>
                    <small>
                    @if($group->start_date && $group->end_date)
                        {{ $group->start_date->format('d.m.Y') }} - {{ $group->end_date->format('d.m.Y') }}
                    @elseif($group->start_date)
                        {{ $group->start_date->format('d.m.Y') }} - Süresiz
                    @elseif($group->end_date)
                        Süresiz - {{ $group->end_date->format('d.m.Y') }}
                    @else
                        Süresiz
                    @endif
                    </small>
                </td>
                <td>
                    <div class="d-flex gap-1 justify-content-end">
                        <button class="btn-material-icon btn-material-icon-info" title="Görüntüle" onclick="window.location.href='{{ route('admin.discount-groups.show', $group) }}'">
                            <span class="material-icons">visibility</span>
                        </button>
                        <button class="btn-material-icon btn-material-icon-warning" title="Düzenle" onclick="window.location.href='{{ route('admin.discount-groups.edit', $group) }}'">
                            <span class="material-icons">edit</span>
                        </button>
                        <form action="{{ route('admin.discount-groups.destroy', $group) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu indirim grubunu silmek istediğinizden emin misiniz?')">
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
    {{ $discountGroups->appends(request()->query())->links() }}
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
            localStorage.setItem('discountGroupsFilterOpen', 'false');
        } else {
            body.classList.add('open');
            header.classList.add('active');
            localStorage.setItem('discountGroupsFilterOpen', 'true');
        }
    }
    
    // Remember filter state
    document.addEventListener('DOMContentLoaded', function() {
        const filterOpen = localStorage.getItem('discountGroupsFilterOpen');
        if (filterOpen === 'true') {
            document.getElementById('filterBody').classList.add('open');
            document.getElementById('filterHeader').classList.add('active');
        }
    });
</script>

@endsection
