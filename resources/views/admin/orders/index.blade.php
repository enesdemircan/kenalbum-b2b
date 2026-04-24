@extends('admin.layout')

@section('content')
<div class="page-header">
    <h1 class="page-title">Siparişler</h1>
    <p class="page-subtitle">Tüm siparişleri görüntüleyin ve yönetin</p>
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
        <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="order_number" class="form-label">Sipariş No</label>
                <input type="text" class="form-control form-control-material" id="order_number" name="order_number" value="{{ $filters['order_number'] ?? '' }}" placeholder="Sipariş no...">
            </div>
            <div class="col-md-3">
                <label for="customer_name" class="form-label">Müşteri Adı</label>
                <input type="text" class="form-control form-control-material" id="customer_name" name="customer_name" value="{{ $filters['customer_name'] ?? '' }}" placeholder="Ad soyad...">
            </div>
            <div class="col-md-3">
                <label for="company_name" class="form-label">Firma Adı</label>
                <input type="text" class="form-control form-control-material" id="company_name" name="company_name" value="{{ $filters['company_name'] ?? '' }}" placeholder="Firma adı...">
            </div>
            <div class="col-md-3">
                <label for="barcode" class="form-label">Barcode</label>
                <input type="text" class="form-control form-control-material" id="barcode" name="barcode" value="{{ $filters['barcode'] ?? '' }}" placeholder="Barcode ile ara...">
            </div>
            <div class="col-md-3">
                <label for="total_price" class="form-label">Min. Fiyat</label>
                <input type="number" class="form-control form-control-material" id="total_price" name="total_price" value="{{ $filters['total_price'] ?? '' }}" placeholder="Min fiyat...">
            </div>
            <div class="col-md-3">
                <label for="overall_status_id" class="form-label">Genel Durum</label>
                <select class="form-select form-control-material" id="overall_status_id" name="overall_status_id">
                    <option value="">Tüm Durumlar</option>
                    <option value="0" {{ ($filters['overall_status_id'] ?? '') == '0' ? 'selected' : '' }}>Onay Bekliyor</option>
                    <option value="1" {{ ($filters['overall_status_id'] ?? '') == '1' ? 'selected' : '' }}>İşlemde</option>
                    <option value="2" {{ ($filters['overall_status_id'] ?? '') == '2' ? 'selected' : '' }}>Teslim Edildi</option>
                    <option value="3" {{ ($filters['overall_status_id'] ?? '') == '3' ? 'selected' : '' }}>İptal Edildi</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="date_from" class="form-label">Başlangıç Tarihi</label>
                <input type="date" class="form-control form-control-material" id="date_from" name="date_from" value="{{ $filters['date_from'] ?? '' }}">
            </div>
            <div class="col-md-3">
                <label for="date_to" class="form-label">Bitiş Tarihi</label>
                <input type="date" class="form-control form-control-material" id="date_to" name="date_to" value="{{ $filters['date_to'] ?? '' }}">
            </div>
            <div class="col-12 d-flex justify-content-end gap-2">
                <button type="submit" class="btn-material btn-material-primary">
                    <span class="material-icons">search</span>
                    Filtrele
                </button>
                <a href="{{ route('admin.orders.index') }}" class="btn-material btn-material-secondary">
                    <span class="material-icons">clear</span>
                    Temizle
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Siparişler Tablosu -->
<div class="material-table-wrapper">
    <table class="material-table">
        <thead>
            <tr>
                <th>Sipariş No</th>
                <th>Firma</th>
                <th>Müşteri</th>
                <th>Ürün Sayısı</th>
                <th>Toplam Fiyat</th>
                <th>Sipariş Durumu</th>
                <th>Tarih</th>
                <th style="width: 250px;">İşlemler</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>
                        <strong style="color: var(--md-primary)">{{ $order->order_number }}</strong>
                    </td>
                    <td>
                        @if($order->user && $order->user->customer && $order->user->customer->unvan)
                            <span class="material-badge material-badge-primary">{{ $order->user->customer->unvan }}</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>
                        <div>
                            <strong>{{ $order->customer_name }} {{ $order->customer_surname }}</strong>
                            <br>
                            <small class="text-muted">{{ $order->customer_phone }}</small>
                        </div>
                    </td>
                    <td>
                        <span class="material-badge material-badge-info">{{ $order->cartItems->count() }} ürün</span>
                    </td>
                    <td>
                        <strong style="color: var(--md-success)">{{ number_format($order->total_price, 2) }} ₺</strong>
                    </td>
                    <td>
                        <span class="material-badge material-badge-{{ $order->status == 0 ? 'info' : ($order->status == 1 ? 'warning' : ($order->status == 2 ? 'success' : 'danger')) }}">
                            {{ $order->status_text }}
                        </span>
                    </td>
                    <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                    <td>
                        <div class="d-flex gap-1 justify-content-end">
                            <a class="btn-material-icon btn-material-icon-info" title="Detay (yeni sekmede)" href="{{ route('admin.orders.show', $order->id) }}" target="_blank" rel="noopener">
                                <span class="material-icons">visibility</span>
                            </a>
                            <button class="btn-material-icon btn-material-icon-success" title="Yazdır" onclick="window.open('{{ route('admin.orders.print', $order->id) }}', '_blank')">
                                <span class="material-icons">print</span>
                            </button>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn-material-icon btn-material-icon-warning dropdown-toggle" data-bs-toggle="dropdown" title="Durum Güncelle">
                                    <span class="material-icons">more_vert</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#" onclick="updateOrderStatus({{ $order->id }}, 0)">
                                        <span class="material-icons" style="font-size:18px;vertical-align:middle">schedule</span> Onay Bekliyor
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="updateOrderStatus({{ $order->id }}, 1)">
                                        <span class="material-icons" style="font-size:18px;vertical-align:middle">pending</span> İşlemde
                                    </a></li>
                                    <li><a class="dropdown-item" href="#" onclick="updateOrderStatus({{ $order->id }}, 2)">
                                        <span class="material-icons" style="font-size:18px;vertical-align:middle">check_circle</span> Teslim Edildi
                                    </a></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="updateOrderStatus({{ $order->id }}, 3)">
                                        <span class="material-icons" style="font-size:18px;vertical-align:middle">cancel</span> İptal
                                    </a></li>
                                </ul>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Sayfalama -->
<div class="material-pagination">
    {{ $orders->appends(request()->query())->links() }}
</div>

<form id="updateStatusForm" method="POST" style="display: none;">
    @csrf
    @method('PUT')
    <input type="hidden" name="status" id="statusInput">
</form>

<script>
    // Filter Accordion Toggle
    function toggleFilterAccordion() {
        const header = document.getElementById('filterHeader');
        const body = document.getElementById('filterBody');
        const isOpen = body.classList.contains('open');
        
        if (isOpen) {
            body.classList.remove('open');
            header.classList.remove('active');
            localStorage.setItem('ordersFilterOpen', 'false');
        } else {
            body.classList.add('open');
            header.classList.add('active');
            localStorage.setItem('ordersFilterOpen', 'true');
        }
    }
    
    // Remember filter state
    document.addEventListener('DOMContentLoaded', function() {
        const filterOpen = localStorage.getItem('ordersFilterOpen');
        if (filterOpen === 'true') {
            document.getElementById('filterBody').classList.add('open');
            document.getElementById('filterHeader').classList.add('active');
        }
    });
    
    // Update Order Status
    function updateOrderStatus(orderId, status) {
        if (confirm('Sipariş durumunu güncellemek istediğinizden emin misiniz?')) {
            document.getElementById('statusInput').value = status;
            document.getElementById('updateStatusForm').action = "{{ route('admin.orders.main-status', ':orderId') }}".replace(':orderId', orderId);
            document.getElementById('updateStatusForm').submit();
        }
    }
</script>

@endsection 