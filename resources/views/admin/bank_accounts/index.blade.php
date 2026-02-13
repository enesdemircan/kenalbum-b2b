@extends('admin.layout')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1 class="page-title">IBAN Hesapları</h1>
        <p class="page-subtitle">Banka hesaplarını yönetin</p>
    </div>
    <a href="{{ route('admin.bank-accounts.create') }}" class="btn-material btn-material-primary">
        <span class="material-icons">add</span>
        Yeni Hesap Ekle
    </a>
</div>

@if(session('success'))
    <div class="material-alert material-alert-success mb-3">
        <span class="material-icons">check_circle</span>
        <span>{{ session('success') }}</span>
    </div>
@endif

<div class="material-card-elevated">
    <div class="material-card-header">
        <h5><span class="material-icons" style="vertical-align:middle;margin-right:8px">account_balance</span>Mevcut Hesaplar</h5>
    </div>
    <div class="material-card-body">
        @if($bankAccounts->count() > 0)
            <div class="material-table-wrapper">
                <table class="material-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Banka Adı</th>
                            <th>IBAN</th>
                            <th>Hesap Sahibi</th>
                            <th>Durum</th>
                            <th style="width: 140px">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bankAccounts as $account)
                            <tr>
                                <td>{{ $account->id }}</td>
                                <td><strong>{{ $account->bank_name }}</strong></td>
                                <td>
                                    <code>{{ $account->iban }}</code>
                                    <button type="button" class="btn-material-icon btn-material-icon-info ms-1" onclick="copyToClipboard('{{ $account->iban }}')" title="IBAN'ı Kopyala">
                                        <span class="material-icons">content_copy</span>
                                    </button>
                                </td>
                                <td>{{ $account->account_name }}</td>
                                <td>
                                    <span class="material-badge material-badge-{{ $account->is_active ? 'success' : 'danger' }}">
                                        {{ $account->is_active ? 'Aktif' : 'Pasif' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1 justify-content-end">
                                        <a href="{{ route('admin.bank-accounts.edit', $account->id) }}" class="btn-material-icon btn-material-icon-warning" title="Düzenle">
                                            <span class="material-icons">edit</span>
                                        </a>
                                        <form action="{{ route('admin.bank-accounts.destroy', $account->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu hesabı silmek istediğinizden emin misiniz?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-material-icon btn-material-icon-danger" title="Sil">
                                                <span class="material-icons">delete</span>
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
            <div class="text-center py-5">
                <span class="material-icons" style="font-size: 64px; color: var(--md-text-disabled);">account_balance</span>
                <p class="text-muted mt-2 mb-3">Henüz banka hesabı eklenmemiş.</p>
                <a href="{{ route('admin.bank-accounts.create') }}" class="btn-material btn-material-primary">
                    <span class="material-icons">add</span>
                    İlk Hesabı Ekle
                </a>
            </div>
        @endif
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'success', title: 'IBAN kopyalandı!', timer: 1500, showConfirmButton: false });
        } else {
            alert('IBAN kopyalandı!');
        }
    }, function(err) {
        console.error('Kopyalama başarısız: ', err);
    });
}
</script>
@endsection
