@extends('frontend.master')

@section('content')
<main>
  <div class="mb-4 pb-4"></div>
  <section class="profile-shell container">

    <div class="profile-shell__head">
      <div>
        <h1 class="profile-shell__heading"><i class="fas fa-id-badge"></i> {{ $personel->name }} — Siparişleri</h1>
        <p class="profile-shell__sub">Bu personelin verdiği siparişlerin listesi.</p>
      </div>
      <a href="{{ route('profile.personels') }}" class="cm-btn cm-btn--ghost"><i class="fas fa-arrow-left"></i> Geri Dön</a>
    </div>

    <div class="row g-4">
      <div class="col-lg-3">
        @include('frontend.profile._sidebar', ['active' => 'personels'])
      </div>

      <div class="col-lg-9">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

        <div class="profile-card">
          <div class="profile-card__head">
            <h3 class="profile-card__title"><i class="fas fa-bag-shopping"></i> Sipariş Geçmişi</h3>
          </div>

          @if($orders->count() > 0)
            <div class="table-responsive">
              <table class="order-detail-table">
                <thead>
                  <tr>
                    <th>Sipariş No</th>
                    <th>Ürünler</th>
                    <th>Toplam</th>
                    <th>Durum</th>
                    <th>Tarih</th>
                    <th style="text-align:right;">Detay</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($orders as $order)
                    <tr>
                      <td><strong style="color:#ea580c;">{{ $order->order_number }}</strong></td>
                      <td>
                        @foreach($order->cartItems as $item)
                          <div><small><strong>{{ $item->product->title ?? '—' }}</strong>@if($item->quantity > 1) <span class="cm-badge cm-badge--company">×{{ $item->quantity }}</span>@endif</small></div>
                        @endforeach
                      </td>
                      <td><strong style="color:#16a34a;">₺{{ number_format($order->total_price, 2, ',', '.') }}</strong></td>
                      <td><span class="order-status-pill s-{{ (int) $order->status }}"><i class="fas fa-circle" style="font-size:.55rem"></i> {{ $order->status_text }}</span></td>
                      <td class="text-muted">{{ $order->created_at->format('d.m.Y H:i') }}</td>
                      <td style="text-align:right;">
                        <a href="{{ route('orders.show', $order->id) }}" class="btn-orange-sm" title="Detay"><i class="fas fa-eye"></i></a>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            <p class="text-muted small mt-3 mb-0">Toplam {{ $orders->total() }} sipariş.</p>
            <div class="d-flex justify-content-center mt-4">{{ $orders->links() }}</div>
          @else
            <div class="profile-empty">
              <i class="fas fa-bag-shopping"></i>
              <h4>Henüz sipariş yok</h4>
              <p>{{ $personel->name }} henüz sipariş vermemiş.</p>
            </div>
          @endif
        </div>
      </div>
    </div>

  </section>
</main>
<br>
@endsection
