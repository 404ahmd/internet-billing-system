@extends('admin.master')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="card">
            <div class="card-body">

                {{-- Alert --}}
                @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <div class="modal fade" id="tambahPelangganModal" tabindex="-1" aria-labelledby="tambahPelangganLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title" id="tambahRouterLabel">Tambah Pelanggan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Tutup">X</button>
                            </div>
                            <form method="POST" action="{{ route('admin.customer.store') }}">

                                <div class="modal-body">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nama</label>
                                        <input type="text" id="name" name="name" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" id="username" name="username" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Telpon</label>
                                        <small>Harus diawali dengan 62</small>
                                        <input type="text" id="phone" name="phone" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="address" class="form-label">Alamat</label>
                                        <input type="text" id="address" name="address" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="group" class="form-label">Grup</label>
                                        <input type="text" id="group" name="group" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="package" class="form-label">Paket</label>
                                        <select name="package" id="package" class="form-control-sm w-100">
                                            {{-- SHOW ALL AVAILABLE PACAKGE --}}
                                            <option value="">-- Pilih Paket --</option>
                                            @forelse($packages as $index => $package)
                                            <option value="{{ $package->id }}">{{ $package->name }}</option>

                                            @empty

                                            @endforelse
                                        </select>

                                    </div>

                                    <div class="mb-3">
                                        <label for="join_date" class="form-label">Tanggal Pendaftaran</label>
                                        <input type="date" id="join_date" name="join_date" class="form-control"
                                            required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select name="status" id="status" class="form-control" required>
                                            <option value="active">Aktif</option>
                                            <option value="inactive">Tidak Aktif</option>
                                            <option value="terminated">Dihentikan</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="notes" class="form-label">Catatan</label>
                                        <input type="text" id="notes" name="notes" class="form-control" required>
                                    </div>
                                </div>


                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Tambah</button>
                                </div>

                            </form>
                        </div>
                    </div>

                </div>
                {{-- Tombol Tambah --}}
                <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal"
                    data-bs-target="#tambahPelangganModal">
                    + Tambah Pelanggan
                </button>

                {{-- FORM SEARCH --}}

                 <form method="GET" action="{{ route('admin.customer.search') }}"
                        class="row g-2 align-items-center mt-3">
                        <div class="col-auto">
                            <label for="filter_status" class="col-form-label">Filter Status:</label>
                        </div>
                        <div class="col-auto">
                            <select name="status" id="filter_status" class="form-select form-select-sm">
                                <option value="">-- Semua --</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak
                                    Aktif</option>
                                <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>
                                    Dihentikan</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-sm btn-secondary">Terapkan</button>
                        </div>
                    </form>

                <form method="GET" action="{{ route('admin.customer.search') }}"
                    class="d-flex align-items-center gap-2 bg-light rounded px-2 py-1 ms-lg-3 mt-2 mt-lg-0 shadow-sm position-relative">

                    <input type="text" class="form-control form-control-sm border-0 bg-transparent"
                        id="customer_search_input" name="keyword" placeholder="🔍 Cari id/nama pelanggan..."
                        value="{{ request('keyword') }}" autocomplete="off">

                    <div id="search_suggestions" class="list-group position-absolute w-100 shadow"
                        style="top: 100%; z-index: 1000;"></div>

                    <button type="submit" class="btn btn-sm btn-primary ms-auto">Cari</button>
                </form>

                {{-- Tabel --}}
                <div class="table-responsive mt-3" style="max-height: 600px;">
                     <h4 class="mt-4">Daftar Pelanggan</h4>
                    <table class="table table-bordered table-hover shadow-sm" style="min-width: 900px;">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Telpon</th>
                                <th>Alamat</th>
                                <th>Paket</th>
                                <th>Grup</th>
                                <th>Tanggal Daftar</th>
                                <th>Status</th>
                                <th>Tanggal Jatuh Tempo</th>
                                <th>Catatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($customers as $index => $customer)
                            <tr>
                                <td>{{ $customers->firstItem() + $index }}</td>
                                <td class="text-primary">{{ $customer->name }}</td>
                                <td>{{ $customer->username }}</td>
                                <td>{{ $customer->phone }}</td>
                                <td>{{ $customer->address }}</td>
                                <td>{{ $customer->package }}</td>
                                <td>{{ $customer->group }}</td>
                                <td>{{ $customer->join_date }}</td>
                                <td>{{ $customer->status }}</td>
                                <td>{{ $customer->due_date }}</td>
                                <td>{{ $customer->notes }}</td>
                                <td>
                                    <a href="{{ route('admin.customer.edit', $customer->id) }}"
                                        class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('admin.customer.destroy', $customer->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Yakin ingin hapus?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="12" class="text-center text-muted">No customers found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="row">
                     <div class="col-md-6">
                        <p class="text-muted">
                            Menampilkan {{ $customers->firstItem() }} sampai {{ $customers->lastItem() }} dari {{
                            $customers->total() }} entri
                        </p>
                    </div>
                    <div class="col-md-6">
                        <div class="float-right">
                            {{ $customers->links('vendor.pagination.bootstrap-4') }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- AUTOCOMPLETE FOR SEARCH CUSTOMER --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('customer_search_input');
    const suggestionBox = document.getElementById('search_suggestions');

    input.addEventListener('input', function () {
        const keyword = input.value;
        if (keyword.length < 2) {
            suggestionBox.innerHTML = '';
            return;
        }

        fetch(`/autocomplete/customer?query=${keyword}`)
            .then(res => res.json())
            .then(data => {
                suggestionBox.innerHTML = '';
                data.forEach(customer => {
                    const item = document.createElement('a');
                    item.href = '#';
                    item.className = 'list-group-item list-group-item-action';
                    item.textContent = `${customer.name} (ID: ${customer.id})`;

                    item.addEventListener('click', function (e) {
                        e.preventDefault();
                        input.value = customer.name;
                        suggestionBox.innerHTML = '';
                    });

                    suggestionBox.appendChild(item);
                });
            });
    });

    document.addEventListener('click', function (e) {
        if (!suggestionBox.contains(e.target) && e.target !== input) {
            suggestionBox.innerHTML = '';
        }
    });
});
</script>
@endsection
