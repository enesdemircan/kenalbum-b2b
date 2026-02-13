@extends('frontend.master')

@section('content')
<main>
<div class="container">
    <div class="row justify-content-center mt-5 mb-5">
        <div class="col-md-8">
            <div class="card bg-white border-0">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-building fa-4x text-warning"></i>
                    </div>
                    
                    <h2 class="card-title mb-4 text-primary">Firma Ataması Gerekli</h2>
                    
                    <div class="alert alert-info" role="alert">
                        <p class="mb-3">
                            <strong>Sipariş verebilmek için hesabınıza bir firma atanması gerekmektedir.</strong>
                        </p>
                        <p class="mb-0">
                            Lütfen yöneticinizle iletişime geçerek firma ataması talebinde bulunun. Firma ataması yapıldıktan sonra sipariş sayfalarına erişebilirsiniz.
                        </p>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('profile.index') }}" class="btn btn-primary">
                            <i class="fas fa-user me-2"></i>
                            Profil Sayfasına Git
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</main>
@endsection
