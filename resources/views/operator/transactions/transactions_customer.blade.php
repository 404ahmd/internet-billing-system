@extends('operator.master')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="card-body">

            <form method="GET" action="{{ route('operator.transaction.search') }}"
                class="d-flex align-items-center gap-2 bg-light rounded px-2 py-1 ms-lg-3 mt-2 mt-lg-0 shadow-sm position-relative">

                <input type="text" class="form-control form-control-sm border-0 bg-transparent"
                    id="customer_search_input" name="keyword" placeholder="🔍 Cari id/nama pelanggan..."
                    value="{{ request('keyword') }}" autocomplete="off">

                <div id="search_suggestions" class="list-group position-absolute w-100 shadow"
                    style="top: 100%; z-index: 1000;"></div>
                <button type="submit" class="btn btn-sm btn-primary ms-auto">Cari</button>

            </form>

            <h4 class="mt-4">Daftar Riwayat Transaksi</h4>
            <div class="table-responsive mt-4" style="max-height: 600px">
                <table class="table table-bordered table-hover shadow-sm" style="min-width: 900px">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Pelanggan</th>
                            <th>Nomor Invoice</th>
                            <th>Jumlah Pembayaran</th>
                            <th>Tanggal Pembayaran</th>
                            <th>Metode Pembayaran</th>
                            <th>Referensi</th>
                            <th>Catatan</th>
                            {{-- <th>Aksi</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $index => $transaction)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $transaction->customer->name ?? '-' }}</td>
                            <td>{{ $transaction->invoice->invoice_number ?? '-' }}</td>
                            <td>{{ number_format($transaction->amount, 0, ',', '.') }}</td>
                            <td>{{ $transaction->payment_date }}</td>
                            <td>{{ $transaction->payment_method }}</td>
                            <td>{{ $transaction->reference }}</td>
                            <td>{{ $transaction->notes }}</td>
                            {{-- <td>
                                <a href="{{ route('transactions.edit', $transaction->id) }}"
                                    class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td> --}}
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada transaksi ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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
