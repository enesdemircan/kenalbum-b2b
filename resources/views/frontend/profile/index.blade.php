@extends('frontend.master')

@section('content')

<main>
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">
      <h2 class="page-title">Hesabım</h2>
      <div class="row">
        <div class="col-lg-3">
          <ul class="account-nav">
            <li><a href="{{ route('profile.index') }}" class="menu-link menu-link_us-s menu-link_active">DASHBOARD</a></li>
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
                <li><a href="{{ route('profile.personels') }}" class="menu-link menu-link_us-s">PERSONELLERİM</a></li>
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
          @if(Auth::user()->roles->contains('id', 3) && Auth::user()->customer)
          <div class="card">
            <div class="card-body">
        
              <p class="card-text">Bakiyeniz: {{ number_format(auth()->user()->customer->balance, 2) }} TL</p>
            </div>
          </div>
          @elseif(Auth::user()->roles->contains('id', 3) && !Auth::user()->customer)
          <div class="alert alert-warning">
            <i class="fa fa-exclamation-triangle"></i> Firma atamanız bulunmamaktadır. Lütfen yönetici ile iletişime geçin.
          </div>
          @endif
          <div class="page-content my-account__dashboard">
            <p>Merhaba <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->name }} değil misiniz? <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Çıkış yapın</a>)</p>
            <p>Hesap panelinizden <a class="unerline-link" href="{{ route('profile.orders') }}">son siparişlerinizi</a> görüntüleyebilir, <a class="unerline-link" href="{{ route('profile.addresses') }}">teslimat ve fatura adreslerinizi</a> yönetebilir ve <a class="unerline-link" href="{{ route('profile.detail') }}">şifrenizi ve hesap detaylarınızı düzenleyebilirsiniz.</a></p>
            
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
          </div>
          
        </div>
      </div>
      
    </section>
  </main>
  <br>
<br>
@endsection