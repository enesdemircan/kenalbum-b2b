@extends('frontend.master')

@section('content')

<main>
<div class="container">
    <div class="row justify-content-center  mt-5 mb-5">
        <div class="col-md-8">
            <div class="card bg-white border-0">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-clock fa-4x text-warning"></i>
                    </div>
                    
                    <h2 class="card-title mb-4 text-primary">Hesabınız Onay Bekliyor</h2>
                    
                    <div class="alert alert-info" role="alert">
                        <p class="mb-3">
                            <strong>Kullanıcı hesabınız moderatörlerimiz tarafından onay sürecindedir.</strong>
                        </p>
                        <p class="mb-0">
                            Hesabınızın onaylanması için lütfen bekleyin. Bu süreç genellikle 24-48 saat içinde tamamlanır.
                        </p>
                    </div>
                    
                    <div class="mt-4">
                        <h5 class="text-muted mb-3">İletişim Bilgileri</h5>
                        <p class="mb-2">
                            <i class="fas fa-envelope me-2"></i>
                            <strong>E-posta:</strong> info@example.com
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-phone me-2"></i>
                            <strong>Telefon:</strong> +90 000 000 00 00
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            <strong>Adres:</strong> İstanbul, Türkiye
                        </p>
                    </div>
                    
                    <div class="mt-5">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Çıkış Yap
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 15px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.card-body {
    padding: 3rem 2rem;
}

.alert {
    border-radius: 10px;
    border: none;
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
}

.btn {
    border-radius: 25px;
    padding: 10px 30px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.fas {
    color: #6c757d;
}

.text-primary {
    color: #007bff !important;
}

.text-warning {
    color: #ffc107 !important;
}
</style>
</main>
@endsection 