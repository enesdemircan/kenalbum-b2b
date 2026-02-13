@extends('admin.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3">Siparişler</h1>
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
        <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3">
            <div class="col">
                <label for="order_number" class="form-label">Sipariş No</label>
                <input type="text" class="form-control" id="order_number" name="order_number" value="{{ $filters['order_number'] ?? '' }}" placeholder="Sipariş no...">
            </div>
            <div class="col">
                <label for="customer_name" class="form-label">Müşteri Adı</label>
                <input type="text" class="form-control" id="customer_name" name="customer_name" value="{{ $filters['customer_name'] ?? '' }}" placeholder="Ad soyad...">
            </div>
            <div class="col">
                <label for="company_name" class="form-label">Firma Adı</label>
                <input type="text" class="form-control" id="company_name" name="company_name" value="{{ $filters['company_name'] ?? '' }}" placeholder="Firma adı...">
            </div>
            <div class="col">
                <label for="barcode" class="form-label">Barcode</label>
                <input type="text" class="form-control" id="barcode" name="barcode" value="{{ $filters['barcode'] ?? '' }}" placeholder="Barcode ile ara...">
            </div>
            <div class="col">
                <label for="total_price" class="form-label">Min. Fiyat</label>
                <input type="number" class="form-control" id="total_price" name="total_price" value="{{ $filters['total_price'] ?? '' }}" placeholder="Min fiyat...">
            </div>
            <div class="col">
                <label for="overall_status_id" class="form-label">Genel Durum</label>
                <select class="form-select" id="overall_status_id" name="overall_status_id">
                    <option value="">Tüm Durumlar</option>
                    <option value="0" {{ ($filters['overall_status_id'] ?? '') == '0' ? 'selected' : '' }}>Onay Bekliyor</option>
                    <option value="1" {{ ($filters['overall_status_id'] ?? '') == '1' ? 'selected' : '' }}>İşlemde</option>
                    <option value="2" {{ ($filters['overall_status_id'] ?? '') == '2' ? 'selected' : '' }}>Teslim Edildi</option>
                    <option value="3" {{ ($filters['overall_status_id'] ?? '') == '3' ? 'selected' : '' }}>İptal Edildi</option>
                </select>
            </div>
            <div class="col">
                <label for="date_from" class="form-label">Başlangıç Tarihi</label>
                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $filters['date_from'] ?? '' }}">
            </div>
            <div class="col">
                <label for="date_to" class="form-label">Bitiş Tarihi</label>
                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $filters['date_to'] ?? '' }}">
            </div>
            <div class="col-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="bi bi-search"></i> Filtrele
                </button>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Temizle
                </a>
            </div>
        </form>
    </div>
</div>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
         
            <th>Sipariş No</th>
            <th>Müşteri</th>
            <th>Firma</th>
            <th>Ürün Sayısı</th>
            <th>Toplam Fiyat</th>
            <th>Sipariş Durumu</th>
            <th>Tarih</th>
            <th style="width: 250px;"></th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $order)
            <tr>
                
                <td>
                    <strong>{{ $order->order_number }}</strong>
                </td>
                <td>
                    <div>
                        <strong>{{ $order->customer_name }} {{ $order->customer_surname }}</strong>
                        <br>
                        <small class="text-muted">{{ $order->customer_phone }}</small>
                    </div>
                </td>
                <td>
                    @if($order->user && $order->user->customer && $order->user->customer->unvan)
                        <span class="badge bg-primary">{{ $order->user->customer->unvan }}</span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td>
                    <span class="badge bg-info">{{ $order->cartItems->count() }} ürün</span>
                </td>
                <td>
                    <strong class="text-success">{{ number_format($order->total_price, 2) }} ₺</strong>
                </td>
             
                <td>
                    <span class="badge {{ $order->status_badge_class }}">
                        {{ $order->status_text }}
                    </span>
                </td>
                <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                <td>
                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info" title="Detay">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="{{ route('admin.orders.print', $order->id) }}" class="btn btn-sm btn-success" title="Yazdır" target="_blank">
                        <i class="bi bi-printer"></i>
                    </a>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-bs-toggle="dropdown" title="Durum Güncelle">
                            <i class="bi bi-gear"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="updateOrderStatus({{ $order->id }}, 0)">Onay Bekliyor</a></li>
                            <li><a class="dropdown-item" href="#" onclick="updateOrderStatus({{ $order->id }}, 1)">İşlemde</a></li>
                            <li><a class="dropdown-item" href="#" onclick="updateOrderStatus({{ $order->id }}, 2)">Teslim Edildi</a></li>
                            <li><a class="dropdown-item" href="#" onclick="updateOrderStatus({{ $order->id }}, 3)">İptal</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Sayfalama -->
<div class="d-flex justify-content-center mt-4">
    {{ $orders->appends(request()->query())->links() }}
</div>

<form id="updateStatusForm" method="POST" style="display: none;">
    @csrf
    @method('PUT')
    <input type="hidden" name="status" id="statusInput">
</form>

<script>
    function updateOrderStatus(orderId, status) {
        if (confirm('Sipariş durumunu güncellemek istediğinizden emin misiniz?')) {
            document.getElementById('statusInput').value = status;
            document.getElementById('updateStatusForm').action = "{{ route('admin.orders.main-status', ':orderId') }}".replace(':orderId', orderId);
            document.getElementById('updateStatusForm').submit();
        }
    }
</script>

@endsection 