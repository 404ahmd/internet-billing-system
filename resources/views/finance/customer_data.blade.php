@extends('finance.master')

@section('content')
    <div class="main-panel">
        <div class="content">
            <div class="card">
                <div class="card-body">

                    <div class="container mx-auto px-4 py-6">
                        <h2 class="text-2xl font-bold mb-4">Data Pelanggan</h2>

                        {{-- FORM SEARCH --}}
                        <form method="GET" action="{{ route('finance.customer.search') }}"
                            class="row g-2 align-items-center mt-3">
                            <div class="col-auto">
                                <label for="filter_status" class="col-form-label">Filter Status:</label>
                            </div>
                            <div class="col-auto">
                                <select name="status" id="filter_status" class="form-select form-select-sm">
                                    <option value="">-- Semua --</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif
                                    </option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak
                                        Aktif</option>
                                    <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>
                                        Dihentikan</option>
                                        <option value="free" {{ request('status') == 'free' ? 'selected' : '' }}>
                                        Gratis</option>
                                        <option value="other" {{ request('status') == 'other' ? 'selected' : '' }}>
                                        Lainnya</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-sm btn-secondary">Terapkan</button>
                            </div>
                        </form>

                        <form method="GET" action="{{ route('finance.customer.search') }}"
                            class="d-flex align-items-center gap-2 bg-light rounded px-2 py-1 ms-lg-3 mt-2 mt-lg-0 shadow-sm position-relative">

                            <input type="text" class="form-control form-control-sm border-0 bg-transparent"
                                id="customer_search_input" name="keyword" placeholder="🔍 Cari id/nama pelanggan..."
                                value="{{ request('keyword') }}" autocomplete="off">

                            <div id="search_suggestions" class="list-group position-absolute w-100 shadow"
                                style="top: 100%; z-index: 1000;"></div>

                            <button type="submit" class="btn btn-sm btn-primary ms-auto">Cari</button>
                        </form>

                        <table class="table-auto w-full border mb-6">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th class="px-4 py-2 border">N0</th>
                                    <th class="px-4 py-2 border">Nama</th>
                                    <th class="px-4 py-2 border">Username</th>
                                    <th class="px-4 py-2 border">Paket</th>
                                    <th class="px-4 py-2 border">Group</th>
                                    <th class="px-4 py-2 border">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($customers as $index => $customer)
                                    <tr>
                                        <td class="px-4 py-2 border">{{ $customers->firstItem() + $index }}</td>
                                        <td class="px-4 py-2 border">{{ $customer->name }}</td>
                                        <td class="px-4 py-2 border">{{ $customer->username }}</td>
                                        <td class="px-4 py-2 border">{{ $customer->package }}</td>
                                        <td class="px-4 py-2 border">{{ $customer->group ?? '-' }}</td>
                                        <td class="px-4 py-2 border">{{ $customer->status }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">Tidak ada data pelanggan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <p class="text-muted">
                                Menampilkan {{ $customers->firstItem() }} sampai {{ $customers->lastItem() }} dari
                                {{ $customers->total() }} entri
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
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('customer_search_input');
            const suggestionBox = document.getElementById('search_suggestions');

            input.addEventListener('input', function() {
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

                            item.addEventListener('click', function(e) {
                                e.preventDefault();
                                input.value = customer.name;
                                suggestionBox.innerHTML = '';
                            });

                            suggestionBox.appendChild(item);
                        });
                    });
            });

            document.addEventListener('click', function(e) {
                if (!suggestionBox.contains(e.target) && e.target !== input) {
                    suggestionBox.innerHTML = '';
                }
            });
        });
    </script>
@endsection
