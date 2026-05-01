@php
  $active = $active ?? '';
  $user = auth()->user();
  $items = [
    ['key' => 'dashboard',  'label' => 'Dashboard',        'icon' => 'fa-gauge',           'route' => route('profile.index'),     'show' => true],
    ['key' => 'orders',     'label' => 'Siparişlerim',     'icon' => 'fa-bag-shopping',    'route' => route('profile.orders'),    'show' => true],
    ['key' => 'addresses',  'label' => 'Adreslerim',       'icon' => 'fa-location-dot',    'route' => route('profile.addresses'), 'show' => true],
    ['key' => 'detail',     'label' => 'Hesap Detayları',  'icon' => 'fa-user-pen',        'route' => route('profile.detail'),    'show' => true],
    ['key' => 'admin',      'label' => 'Yönetim Paneli',   'icon' => 'fa-screwdriver-wrench', 'route' => route('admin.dashboard'),    'show' => $user && $user->hasRole(1)],
    ['key' => 'customers',  'label' => 'Müşteri Listesi',  'icon' => 'fa-people-group',    'route' => $user && $user->hasRole('Satış Müdürü') ? route('admin.customers.index') : '#', 'show' => $user && $user->hasRole('Satış Müdürü')],
    ['key' => 'personels',  'label' => 'Personellerim',    'icon' => 'fa-id-badge',        'route' => $user && $user->hasRole(3) ? route('profile.personels') : '#', 'show' => $user && $user->hasRole(3)],
  ];
@endphp

<aside class="profile-sidebar">
  <div class="profile-sidebar__user">
    <div class="profile-sidebar__avatar">{{ mb_strtoupper(mb_substr(optional($user)->name ?? 'K', 0, 1, 'UTF-8'), 'UTF-8') }}</div>
    <div class="profile-sidebar__userinfo">
      <strong>{{ optional($user)->name }}</strong>
      <em>{{ optional($user)->email }}</em>
    </div>
  </div>

  <nav class="profile-sidebar__nav">
    @foreach($items as $item)
      @if($item['show'])
        <a href="{{ $item['route'] }}" class="profile-sidebar__link {{ $active === $item['key'] ? 'is-active' : '' }}">
          <i class="fas {{ $item['icon'] }}"></i>
          <span>{{ $item['label'] }}</span>
        </a>
      @endif
    @endforeach

    <form method="POST" action="{{ route('logout') }}" class="profile-sidebar__logoutForm">
      @csrf
      <button type="submit" class="profile-sidebar__link profile-sidebar__link--logout">
        <i class="fas fa-arrow-right-from-bracket"></i>
        <span>Çıkış Yap</span>
      </button>
    </form>
  </nav>
</aside>
