@extends('operator.master')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="card">
            <div class="card-body">

                @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"
                        aria-placeholder="X"></button>
                </div>
                @endif

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            PPP Secrets
                        </h5>
                    </div>

                    <div class="card-body">
                        <form action="{{ route('operator.ppp-secret.store') }}" method="POST">
                            @csrf

                            {{-- router --}}
                            <div class="form-group mb-3">
                                <label for="router_id">Router</label>
                                <select class="form-control" name="router_id" id="router_id" required>
                                    @foreach ($routers as $router)
                                    <option value="{{ $router->id }}">{{ $router->name }} ({{$router->host}})</option>
                                    @endforeach
                                </select>
                            </div>


                            {{-- Username --}}
                            <div class="form-group mb-3">
                                <label for="name">Username PPPoE</label>
                                <input type="text" class="form-control" name="name" id="name" required>
                            </div>

                            {{-- Password --}}
                            <div class="form-group mb-3">
                                <label for="password">Password</label>
                                <input type="text" class="form-control" name="password" id="password" required>
                            </div>

                            {{-- Service --}}
                            <div class="form-group mb-3">
                                <label for="service">Service</label>
                                <select class="form-control" name="service" id="service">
                                    <option value="pppoe" selected>pppoe</option>
                                    <option value="pptp">pptp</option>
                                    <option value="l2tp">l2tp</option>
                                    <option value="ovpn">ovpn</option>
                                    <option value="sstp">sstp</option>
                                </select>
                            </div>

                            {{-- Profile --}}
                            <div class="form-group mb-3">
                                <label for="profile">Profile</label>
                                {{-- <input type="text" class="form-control" name="profile" id="profile" required> --}}

                                <select class="form-control" name="profile" id="profile">
                                    @foreach ($profiles as $profile)
                                    <option value="{{ $profile->name }}">{{ $profile->name }} {{($profile->rate_limit)}}
                                    </option>
                                    @endforeach
                                </select>

                            </div>

                            {{-- Local Address --}}
                            <div class="form-group mb-3">
                                <label for="local_address">Local Address (Opsional)</label>
                                <input type="text" class="form-control" name="local_address" id="local_address"
                                    placeholder="Contoh: 192.168.88.1">
                            </div>

                            {{-- Remote Address --}}
                            <div class="form-group mb-3">
                                <label for="remote_address">Remote Address (Opsional)</label>
                                <input type="text" class="form-control" name="remote_address" id="remote_address"
                                    placeholder="Contoh: 192.168.88.100">
                            </div>

                            {{-- Comment --}}
                            <div class="form-group mb-4">
                                <label for="comment">Komentar (Opsional)</label>
                                <input type="text" class="form-control" name="comment" id="comment"
                                    placeholder="Contoh: Pelanggan A">
                            </div>

                            {{-- Submit Button --}}
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Simpan PPP Secret</button>
                            </div>
                        </form>

                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Daftar Secrets</h5>
                    </div>
                    <div class="card-body">

                        <form action="{{ route('operator.ppp-secret.search') }}" method="GET" class="form-inline mb-3">
                            <input type="text" name="name" class="form-control mr-2" placeholder="Cari nama PPP Secret"
                                value="{{ request('name') }}">
                            <button type="submit" class="btn btn-primary">Cari</button>
                        </form>


                        @if ($secrets->isEmpty())
                        <div class="alert alert-info">Belum ada secret yang dibuat</div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="router-table">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Password</th>
                                        <th>Service</th>
                                        <th>Profile</th>
                                        <th>Local Address</th>
                                        <th>Remote Address</th>
                                        <th>Coment</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($secrets as $index => $secret)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $secret->name }}</td>
                                        <td>{{ $secret->password }}</td>
                                        <td>{{ $secret->service }}</td>
                                        <td>{{ $secret->profile }}</td>
                                        <td>{{ $secret->local_address }}</td>
                                        <td>{{ $secret->remote_address }}</td>
                                        <td>{{ $secret->comment }}</td>

                                        <td>
                                            <form action="{{route('operator.ppp-secret.remove', $secret->id)}}"
                                                method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus router ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash-alt"></i> Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="row">
                                <div class="col-md-6">
                                    <p class="text-muted">
                                        Menampilkan {{ $secrets->firstItem() }} sampai {{ $secrets->lastItem() }} dari
                                        {{
                                        $secrets->total() }} entri
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <div class="float-right">
                                        {{ $secrets->links('vendor.pagination.bootstrap-4') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
