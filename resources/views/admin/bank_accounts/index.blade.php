@extends('admin.layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>IBAN Hesapları</h1>
        <a href="{{ route('admin.bank-accounts.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> Yeni Hesap Ekle
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Mevcut Hesaplar</h5>
        </div>
        <div class="card-body">
            @if($bankAccounts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Banka Adı</th>
                                <th>IBAN</th>
                                <th>Hesap Sahibi</th>
                                <th>Durum</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bankAccounts as $account)
                                <tr>
                                    <td>{{ $account->id }}</td>
                                    <td>{{ $account->bank_name }}</td>
                                    <td>
                                        <code>{{ $account->iban }}</code>
                                        <button class="btn btn-sm btn-outline-secondary ms-2" 
                                                onclick="copyToClipboard('{{ $account->iban }}')" 
                                                title="IBAN'ı Kopyala">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </td>
                                    <td>{{ $account->account_name }}</td>
                                    <td>
                                        <span class="badge bg-{{ $account->is_active ? 'success' : 'danger' }}">
                                            {{ $account->is_active ? 'Aktif' : 'Pasif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.bank-accounts.edit', $account->id) }}" 
                                               class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Düzenle
                                            </a>
                                            <form action="{{ route('admin.bank-accounts.destroy', $account->id) }}" 
                                                  method="POST" 
                                                  style="display: inline;"
                                                  onsubmit="return confirm('Bu hesabı silmek istediğinizden emin misiniz?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i> Sil
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <p class="text-muted">Henüz banka hesabı eklenmemiş.</p>
                    <a href="{{ route('admin.bank-accounts.create') }}" class="btn btn-primary">
                        İlk Hesabı Ekle
                    </a>
                </div>
            @endif
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('IBAN kopyalandı!');
            }, function(err) {
                console.error('Kopyalama başarısız: ', err);
            });
        }
    </script>
@endsection 