@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle">Hoş geldiniz, {{ Auth::user()->name }}! Sisteminizin genel görünümü</p>
</div>

<!-- İstatistik Kartları -->
<div class="row g-4 mb-4">
    <!-- Toplam Sipariş -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card primary">
            <div class="stat-icon primary">
                <span class="material-icons">shopping_cart</span>
            </div>
            <div class="stat-value">{{ number_format($totalOrders) }}</div>
            <div class="stat-label">Toplam Sipariş</div>
            <div class="stat-change positive">
                <span class="material-icons" style="font-size:14px;vertical-align:middle">arrow_upward</span>
                Bugün: {{ $todayOrders }}
            </div>
        </div>
    </div>
    
    <!-- Toplam Ciro -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card success">
            <div class="stat-icon success">
                <span class="material-icons">attach_money</span>
            </div>
            <div class="stat-value">{{ number_format($totalRevenue, 0, ',', '.') }} ₺</div>
            <div class="stat-label">Toplam Ciro</div>
            <div class="stat-change positive">
                <span class="material-icons" style="font-size:14px;vertical-align:middle">trending_up</span>
                Bu Ay: {{ number_format($monthRevenue, 0, ',', '.') }} ₺
            </div>
        </div>
    </div>
    
    <!-- Firma Sayısı -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card info">
            <div class="stat-icon info">
                <span class="material-icons">business</span>
            </div>
            <div class="stat-value">{{ number_format($totalCustomers) }}</div>
            <div class="stat-label">Toplam Firma</div>
            <div class="stat-change {{ $negativeBalanceCustomers > 0 ? 'negative' : 'positive' }}">
                <span class="material-icons" style="font-size:14px;vertical-align:middle">
                    {{ $negativeBalanceCustomers > 0 ? 'warning' : 'check_circle' }}
                </span>
                Borçlu: {{ $negativeBalanceCustomers }}
            </div>
        </div>
    </div>
    
    <!-- Toplam Bakiye -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card {{ $totalCustomerBalance >= 0 ? 'success' : 'danger' }}">
            <div class="stat-icon {{ $totalCustomerBalance >= 0 ? 'success' : 'danger' }}">
                <span class="material-icons">account_balance_wallet</span>
            </div>
            <div class="stat-value">{{ number_format($totalCustomerBalance, 0, ',', '.') }} ₺</div>
            <div class="stat-label">Toplam Firma Bakiyesi</div>
            <div class="stat-change positive">
                <span class="material-icons" style="font-size:14px;vertical-align:middle">payments</span>
                Tahsilat: {{ number_format($totalCollections, 0, ',', '.') }} ₺
            </div>
        </div>
    </div>
</div>

<!-- Ek İstatistikler -->
<div class="row g-4 mb-4">
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="material-card" style="padding:20px;text-align:center">
            <div class="material-icons" style="font-size:36px;color:var(--primary-color);margin-bottom:8px">inventory_2</div>
            <div style="font-size:24px;font-weight:500;margin-bottom:4px">{{ number_format($productsCount) }}</div>
            <div style="font-size:14px;color:#757575">Ürünler</div>
        </div>
    </div>
    
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="material-card" style="padding:20px;text-align:center">
            <div class="material-icons" style="font-size:36px;color:var(--success-color);margin-bottom:8px">category</div>
            <div style="font-size:24px;font-weight:500;margin-bottom:4px">{{ number_format($mainCategoriesCount) }}</div>
            <div style="font-size:14px;color:#757575">Kategoriler</div>
        </div>
    </div>
    
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="material-card" style="padding:20px;text-align:center">
            <div class="material-icons" style="font-size:36px;color:var(--info-color);margin-bottom:8px">people</div>
            <div style="font-size:24px;font-weight:500;margin-bottom:4px">{{ number_format($totalUsers) }}</div>
            <div style="font-size:14px;color:#757575">Kullanıcılar</div>
        </div>
    </div>
    
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="material-card" style="padding:20px;text-align:center">
            <div class="material-icons" style="font-size:36px;color:var(--warning-color);margin-bottom:8px">today</div>
            <div style="font-size:24px;font-weight:500;margin-bottom:4px">{{ number_format($todayOrders) }}</div>
            <div style="font-size:14px;color:#757575">Bugün Sipariş</div>
        </div>
    </div>
    
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="material-card" style="padding:20px;text-align:center">
            <div class="material-icons" style="font-size:36px;color:var(--success-color);margin-bottom:8px">calendar_month</div>
            <div style="font-size:24px;font-weight:500;margin-bottom:4px">{{ number_format($monthOrders) }}</div>
            <div style="font-size:14px;color:#757575">Bu Ay Sipariş</div>
        </div>
    </div>
    
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="material-card" style="padding:20px;text-align:center">
            <div class="material-icons" style="font-size:36px;color:var(--primary-color);margin-bottom:8px">monetization_on</div>
            <div style="font-size:24px;font-weight:500;margin-bottom:4px">{{ number_format($todayRevenue, 0, ',', '.') }} ₺</div>
            <div style="font-size:14px;color:#757575">Bugün Ciro</div>
        </div>
    </div>
</div>

<!-- Grafikler -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="material-card" style="padding:24px">
            <h5 style="margin-bottom:24px;display:flex;align-items:center;gap:8px">
                <span class="material-icons" style="color:var(--primary-color)">show_chart</span>
                Aylık Sipariş & Ciro Grafiği (Son 12 Ay)
            </h5>
            <canvas id="monthlyChart" style="max-height:350px"></canvas>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="material-card" style="padding:24px">
            <h5 style="margin-bottom:24px;display:flex;align-items:center;gap:8px">
                <span class="material-icons" style="color:var(--warning-color)">pie_chart</span>
                Sipariş Durumları
            </h5>
            <canvas id="statusChart" style="max-height:300px"></canvas>
        </div>
    </div>
</div>

<!-- Firma Bakiyeleri -->
<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="material-card" style="padding:24px">
            <h5 style="margin-bottom:20px;display:flex;align-items:center;gap:8px">
                <span class="material-icons" style="color:var(--success-color)">trending_up</span>
                En Yüksek Bakiyeli Firmalar
            </h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Firma</th>
                            <th>Firma ID</th>
                            <th class="text-end">Bakiye</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topCustomersByBalance as $customer)
                        <tr>
                            <td>
                                <a href="{{ route('admin.customers.edit', $customer->id) }}" style="text-decoration:none;color:#212121">
                                    {{ $customer->unvan }}
                                </a>
                            </td>
                            <td><span class="badge bg-light text-dark">{{ $customer->firma_id }}</span></td>
                            <td class="text-end">
                                <span style="color:var(--success-color);font-weight:500">
                                    {{ number_format($customer->balance, 2, ',', '.') }} ₺
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">Pozitif bakiyeli firma yok</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="material-card" style="padding:24px">
            <h5 style="margin-bottom:20px;display:flex;align-items:center;gap:8px">
                <span class="material-icons" style="color:var(--danger-color)">trending_down</span>
                En Borçlu Firmalar
            </h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Firma</th>
                            <th>Firma ID</th>
                            <th class="text-end">Bakiye</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mostInDebtCustomers as $customer)
                        <tr>
                            <td>
                                <a href="{{ route('admin.customers.edit', $customer->id) }}" style="text-decoration:none;color:#212121">
                                    {{ $customer->unvan }}
                                </a>
                            </td>
                            <td><span class="badge bg-light text-dark">{{ $customer->firma_id }}</span></td>
                            <td class="text-end">
                                <span style="color:var(--danger-color);font-weight:500">
                                    {{ number_format($customer->balance, 2, ',', '.') }} ₺
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">Borçlu firma yok</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Son Siparişler -->
<div class="row g-4">
    <div class="col-12">
        <div class="material-card" style="padding:24px">
            <h5 style="margin-bottom:20px;display:flex;align-items:center;gap:8px">
                <span class="material-icons" style="color:var(--primary-color)">receipt_long</span>
                Son Siparişler
            </h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Sipariş No</th>
                            <th>Firma</th>
                            <th>Durum</th>
                            <th class="text-end">Tutar</th>
                            <th>Tarih</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('admin.orders.show', $order->id) }}" style="text-decoration:none;color:var(--primary-color);font-weight:500">
                                    #{{ $order->order_number }}
                                </a>
                            </td>
                            <td>{{ $order->user->customer->unvan ?? 'N/A' }}</td>
                            <td>
                                @if($order->orderStatus)
                                    <span class="badge" style="background-color:{{ $order->orderStatus->color ?? '#6c757d' }}">
                                        {{ $order->orderStatus->name }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Bilinmiyor</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <strong>{{ number_format($order->total_price, 2, ',', '.') }} ₺</strong>
                            </td>
                            <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                    <span class="material-icons" style="font-size:16px;vertical-align:middle">visibility</span>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Henüz sipariş yok</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($recentOrders->count() >= 10)
            <div class="text-center mt-3">
                <a href="{{ route('admin.orders.index') }}" class="btn btn-primary">
                    Tüm Siparişleri Görüntüle
                    <span class="material-icons" style="font-size:18px;vertical-align:middle;margin-left:4px">arrow_forward</span>
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Aylık Sipariş & Ciro Grafiği
const monthlyData = @json($monthlyOrders);
const months = monthlyData.map(item => {
    const [year, month] = item.month.split('-');
    return new Date(year, month - 1).toLocaleDateString('tr-TR', { month: 'short', year: 'numeric' });
});
const orderCounts = monthlyData.map(item => item.count);
const revenues = monthlyData.map(item => parseFloat(item.revenue));

const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: months,
        datasets: [{
            label: 'Sipariş Adedi',
            data: orderCounts,
            borderColor: '#1976d2',
            backgroundColor: 'rgba(25, 118, 210, 0.1)',
            yAxisID: 'y',
            tension: 0.4,
            fill: true
        }, {
            label: 'Ciro (₺)',
            data: revenues,
            borderColor: '#388e3c',
            backgroundColor: 'rgba(56, 142, 60, 0.1)',
            yAxisID: 'y1',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            legend: {
                display: true,
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (context.parsed.y !== null) {
                            if (context.datasetIndex === 1) {
                                label += new Intl.NumberFormat('tr-TR', { style: 'currency', currency: 'TRY' }).format(context.parsed.y);
                            } else {
                                label += context.parsed.y;
                            }
                        }
                        return label;
                    }
                }
            }
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Sipariş Adedi'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Ciro (₺)'
                },
                grid: {
                    drawOnChartArea: false,
                }
            }
        }
    }
});

// Sipariş Durumları Grafiği
const statusData = @json($ordersByStatus);
const statusLabels = statusData.map(item => {
    const statusNames = {
        '1': 'Beklemede',
        '2': 'Onaylandı',
        '3': 'İptal',
        '4': 'Tamamlandı',
        '5': 'Kargoda'
    };
    return statusNames[item.status] || 'Bilinmiyor';
});
const statusCounts = statusData.map(item => item.count);
const statusColors = ['#f57c00', '#1976d2', '#d32f2f', '#388e3c', '#0288d1'];

const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: statusLabels,
        datasets: [{
            data: statusCounts,
            backgroundColor: statusColors,
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'bottom',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += context.parsed + ' sipariş';
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                        label += ` (${percentage}%)`;
                        return label;
                    }
                }
            }
        }
    }
});
</script>
@endpush
