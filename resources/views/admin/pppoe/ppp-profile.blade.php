@extends('admin.master')

@section('content')
    <div class="main-panel">
        <div class="content">
            <div class="card">
                <div class="card-body">

                   
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" aria-placeholder="X"></button>
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
                        <div class="card-header text-white bg-primary"> <h5 class="mb-0">Tambah PPP Profile</h5></div>

                        <div class="card-body">
                            <form action="{{ route('admin.ppp-profile.store') }}" method="POST">
                                    @csrf

                                    <div class="form-group">
                                        <label>Router</label>
                                        <select name="router_id" class="form-control" required>
                                            <option value="">-- Pilih Router --</option>
                                            @foreach ($routers as $router)
                                                <option value="{{ $router->id }}">{{ $router->name }}
                                                    ({{ $router->host }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Nama Profile</label>
                                        <input type="text" name="name" class="form-control"
                                            placeholder="ex: pppoe_default" required>
                                    </div>

                                    <div class="form-group">
                                        <label>IP Pool Lokal</label>
                                        <select name="local_address" class="form-control" required>
                                            @foreach (\App\Models\IpPool::all() as $pool)
                                                <option value="{{ $pool->id }}">{{ $pool->name }}
                                                    ({{ $pool->range }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>IP Pool Remote</label>
                                        <select name="remote_address" class="form-control" required>
                                            @foreach (\App\Models\IpPool::all() as $pool)
                                                <option value="{{ $pool->id }}">{{ $pool->name }}
                                                    ({{ $pool->range }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>Rate Limit (optional)</label>
                                        <input type="text" name="rate_limit" class="form-control"
                                            placeholder="512k/512k">
                                    </div>

                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg primary text-white"><h5>Daftar Profile</h5></div>
                        <div class="card-body">
                            @if ($profiles->isEmpty())
                                <div class="alert alert-info">Belum ada profile yang dibuat</div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped" id="router-table">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Router</th>
                                                <th>Local Address</th>
                                                <th>Remote Address</th>
                                                <th>Rate Limit</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($profiles as $index => $profile)
                                                <tr>
                                                    <td>{{ $index + 1}}</td>
                                                    <td>{{ $profile->name }}</td>
                                                    <td>{{ $profile->router->name ?? "-"}}</td>
                                                    <td>{{$profile->localPool->range ?? "-"}}</td>
                                                    <td>{{$profile->remotePool->range ?? "-"}}</td>
                                                    <td>{{$profile->rate_limit}}</td>
                                                    
                                                    <td>
                                                        <form action="{{route('admin.ppp-profile.remove', $profile->id)}}" method="POST"
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
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


@endsection
