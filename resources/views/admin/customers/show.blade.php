@extends('admin.layout')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Firma Detayları</h5>
                        <div>
                            <a href="{{ route('admin.customers.edit', $customer->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Düzenle
                            </a>
                            <a href="{{ route('admin.customers.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Geri
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">ID:</th>
                                    <td>{{ $customer->id }}</td>
                                </tr>
                                <tr>
                                    <th>Firma ID:</th>
                                    <td><code>{{ $customer->firma_id }}</code></td>
                                </tr>
                                <tr>
                                    <th>Ünvan:</th>
                                    <td>{{ $customer->unvan }}</td>
                                </tr>
                                <tr>
                                    <th>Telefon:</th>
                                    <td>{{ $customer->phone }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">E-posta:</th>
                                    <td>{{ $customer->email }}</td>
                                </tr>
                                <tr>
                                    <th>Vergi Dairesi:</th>
                                    <td>{{ $customer->vergi_dairesi }}</td>
                                </tr>
                                <tr>
                                    <th>Vergi Numarası:</th>
                                    <td>{{ $customer->vergi_numarasi }}</td>
                                </tr>
                                <tr>
                                    <th>Bakiye:</th>
                                    <td>
                                        <span class="badge {{ $customer->balance > 0 ? 'bg-success' : ($customer->balance < 0 ? 'bg-danger' : 'bg-secondary') }} fs-6">
                                            {{ $customer->formatted_balance }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Oluşturulma:</th>
                                    <td>{{ $customer->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <h6>Adres:</h6>
                        <div class="border rounded p-3 bg-light">
                            {{ $customer->adres }}
                        </div>
                    </div>

                    @if($customer->users->count() > 0)
                        <div class="mt-4">
                            <h6>Bu Firmaya Atanmış Kullanıcılar ({{ $customer->users->count() }}):</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Ad</th>
                                            <th>E-posta</th>
                                            <th>Kayıt Tarihi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($customer->users as $user)
                                            <tr>
                                                <td>{{ $user->id }}</td>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ $user->created_at->format('d.m.Y') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="mt-4">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Bu firmaya henüz kullanıcı atanmamış.
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection 