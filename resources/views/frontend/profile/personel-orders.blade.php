@extends('frontend.master')

@section('content')
<main>
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="page-title">{{ $personel->name }} - Siparişleri</h2>
            <a href="{{ route('profile.personels') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Geri Dön
            </a>
        </div>
        
        <div class="row">
            <div class="col-lg-3">
                <ul class="account-nav">
                    <li><a href="{{ route('profile.index') }}" class="menu-link menu-link_us-s">DASHBOARD</a></li>
                    <li><a href="{{ route('profile.orders') }}" class="menu-link menu-link_us-s">SİPARİŞLERİM</a></li>
                    <li><a href="{{ route('profile.addresses') }}" class="menu-link menu-link_us-s">ADRESLERİM</a></li>
                    <li><a href="{{ route('profile.detail') }}" class="menu-link menu-link_us-s">HESAP DETAYLARI</a></li>
                    @if(auth()->user()->hasRole(1))
                    <li><a href="{{ route('admin.dashboard') }}" class="menu-link menu-link_us-s">YÖNETİM PANELİ</a></li>
                    @endif
                    @if(auth()->user()->hasRole('Satış Müdürü'))
                    <li><a href="{{ route('admin.customers.index') }}" class="menu-link menu-link_us-s">MÜŞTERİ LİSTESİ</a></li>
                    @endif
                    @if(auth()->user()->hasRole(3))
                    <li><a href="{{ route('profile.personels') }}" class="menu-link menu-link_us-s menu-link_active">PERSONELLERİM</a></li>
                    @endif
                    <li>
                        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="menu-link menu-link_us-s border-0 bg-transparent" style="width: 100%; text-align: left; padding: 0; font-size: 14px;font-weight: 500;text-transform: uppercase;">
                                Çıkış Yap
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            <div class="col-lg-9">
                <div class="page-content my-account__dashboard">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Sipariş No</th>
                                        <th>Ürünler</th>
                                        <th>Toplam Fiyat</th>
                                        <th>Durum</th>
                                        <th>Tarih</th>
                                        <th style="width: 100px;">İşlem</th>
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
                                                    @foreach($order->cartItems as $item)
                                                        <div class="mb-1">
                                                            <small>
                                                                <strong>{{ $item->product->title ?? 'Ürün Bulunamadı' }}</strong>
                                                                @if($item->quantity > 1)
                                                                    <span class="badge bg-secondary">x{{ $item->quantity }}</span>
                                                                @endif
                                                            </small>
                                                        </div>
                                                    @endforeach
                                                </div>
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
                                                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-info" title="Detay">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="mt-3">
                            <p class="text-muted">Toplam {{ $orders->total() }} sipariş bulundu.</p>
                        </div>
                        
                        <!-- Sayfalama -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Bu personelin henüz siparişi bulunmuyor.
                            </div>
                            <p class="text-muted">{{ $personel->name }} henüz sipariş vermemiş.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</main>
<br>
<br>
@endsection 