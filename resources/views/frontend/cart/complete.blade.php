@extends('frontend.master')

@section('content')
<main>
    <div class="mb-4 pb-4"></div>
    <section class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h2 class="card-title text-success">SİPARİŞİNİZ BAŞARILI OLUŞTURULDU!</h2>
                        <p class="card-text">
                            Siparişiniz başarıyla alındı. Sipariş numaranız: <strong>{{ session('success') ? explode(': ', session('success'))[1] : 'N/A' }}</strong>
                        </p>
                        <p class="card-text">
                            Siparişiniz onaylandıktan sonra havale bilgileri size e-posta ile gönderilecektir.
                        </p>
                        <div class="mt-4">
                            <a href="{{ route('profile.orders') }}" class="btn btn-primary me-2">
                                <i class="fas fa-list"></i> Siparişlerim
                            </a>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-shopping-cart"></i> Alışverişe Devam Et
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <br>
</main>
@endsection 