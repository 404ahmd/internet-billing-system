@extends('admin.master')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            {{-- Flash Messages --}}
            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            @endif

            @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            @endif

            {{-- Form Tambah IP Pool --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Tambah IP Pool</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('operator.ip-pool.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">Nama Pool</label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: pppoe-pool"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="range">Range IP</label>
                            <input type="text" name="range" class="form-control"
                                placeholder="192.168.100.2-192.168.100.254" required>
                        </div>

                        <div class="form-group">
                            <label for="router_id">Pilih Router</label>
                            <select name="router_id" class="form-control" required>
                                <option value="">-- Pilih Router --</option>
                                @foreach ($routers as $router)
                                <option value="{{ $router->id }}">{{ $router->name }} ({{ $router->ip }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Tambah IP Pool</button>
                    </form>
                </div>
            </div>

            {{-- Tabel Data IP Pool --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Daftar IP Pool</h5>
                </div>
                <div class="card-body p-0">
                    @if ($ip_pools->isEmpty())
                    <div class="p-3">Belum ada data IP Pool.</div>
                    @else
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pool</th>
                                    <th>Range</th>
                                    <th>Router</th>
                                    <th>Dibuat Pada</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ip_pools as $index => $pool)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $pool->name }}</td>
                                    <td>{{ $pool->range }}</td>
                                    <td>{{ $pool->router->name ?? '-' }}</td>
                                    <td>{{ $pool->created_at }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('admin.ip-pool.destroy', $pool->id) }}"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus IP Pool ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
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
@endsection
