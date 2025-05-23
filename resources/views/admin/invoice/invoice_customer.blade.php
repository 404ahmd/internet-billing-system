@extends('admin.master')

@section('content')

<div class="main-panel">
    <div class="content">
        <div class="card-body">
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
            <h4>Daftar Invoice</h4>

            <form method="GET" action="{{ route('admin.invoices.search') }}">
                <div class="mb-3 position-relative">
                    <input type="text" name="customer_name" id="customer_search_input" class="form-control"
                        placeholder="Cari invoice berdasarkan nama customer...">
                    <div id="search_suggestions" class="list-group position-absolute w-100 z-3"
                        style="max-height: 200px; overflow-y: auto;"></div>
                </div>
                <button type="submit" class="btn btn-primary">Cari</button>
            </form>



            <div class="table-responsive" style="max-height: 600px">
                <table class="table table-bordered table-hover shadow-sm" style="min-width:900px">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>ID Pelanggan</th>
                            <th>ID Paket</th>
                            <th>Nomor Invoice</th>
                            <th>Tanggal Penerbitan</th>
                            <th>Tanggal Jatuh Tempo</th>
                            <th>Jumlah</th>
                            <th>Jumlah Pajak</th>
                            <th>Jumlah Total</th>
                            <th>Status Pembayaran</th>
                            <th>Tanggal Pembayaran</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $index => $invoice)

                        <tr>
                            <td>{{ $invoices->firstItem() + $index }}</td>
                            <td>{{ $invoice->customer->name }}</td>
                            <td>{{ $invoice->package->name }}</td>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->issue_date }}</td>
                            <td>{{ $invoice->due_date }}</td>
                            <td>{{ $invoice->amount }}</td>
                            <td>{{ $invoice->tax_amount }}</td>
                            <td>{{ $invoice->total_amount }}</td>
                            <td>{{ $invoice->status }}</td>
                            <td>{{ $invoice->paid_at }}</td>
                            <td>{{ $invoice->notes }}</td>
                            <td>
                                <a href="{{ route('admin.invoices.edit', $invoice->id) }}"
                                    class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('admin.invoice.delete', $invoice->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin hapus?')" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Yakin ingin menghapus customer ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty

                        @endforelse
                    </tbody>
                </table>
            </div>

              <div class="row mt-4">
                    <div class="col-md-6">
                        <p class="text-muted">
                            Menampilkan {{ $invoices->firstItem() }} sampai {{ $invoices->lastItem() }} dari {{
                            $invoices->total() }} entri
                        </p>
                    </div>
                    <div class="col-md-6">
                        <div class="float-right">
                            {{ $invoices->links('vendor.pagination.bootstrap-4') }}
                        </div>
                    </div>
                </div>

        </div>
    </div>
</div>

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
