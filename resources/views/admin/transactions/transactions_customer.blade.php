@extends('admin.master')

@section('content')
    <div class="main-panel">
        <div class="content">
            <div class="card-body">
                <h4>Daftar Riwayat Transaksi</h4>
                <form method="GET" action="{{ route('admin.transaction.search') }}"
                class="d-flex align-items-center gap-2 bg-light rounded px-2 py-1 ms-lg-3 mt-2 mt-lg-0 shadow-sm">
                <input class="form-control form-control-sm border-0 bg-transparent" type="search" name="keyword"
                    placeholder="🔍 Cari id/nama pelanggan..." value="{{ request('keyword') }}" aria-label="Search">
                <button type="submit" class="btn btn-sm btn-primary ms-auto">Cari</button>
            </form>

                <div class="table-responsive" style="max-height: 600px">
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
                                        <a href="{{ route('transactions.edit', $transaction->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                        <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')" class="d-inline">
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
                <div class="row mt-4">
                    <div class="col-md-6">
                        <p class="text-muted">
                            Menampilkan {{ $transactions->firstItem() }} sampai {{ $transactions->lastItem() }} dari {{
                            $transactions->total() }} entri
                        </p>
                    </div>
                    <div class="col-md-6">
                        <div class="float-right">
                            {{ $transactions->links('vendor.pagination.bootstrap-4') }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
