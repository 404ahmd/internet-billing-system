@extends('finance.master')
@section('content')

<div class="main-panel">
    <div class="content">
        <div class="card">
            <div class="card-body">
                <div class="container-fluid px-4 py-4">
                    <h2 class="text-2xl font-bold mb-4">Laporan Aktivitas Transaksi</h2>

                    <div class="table-responsive" s">
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
                                <td class="text-center">{{ $transactions->firstItem() + $index }}</td>
                                <td>{{ $transaction->customer->name ?? '-' }}</td>
                                <td>{{ $transaction->invoice->invoice_number ?? '-' }}</td>
                                <td>{{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                <td>{{ $transaction->payment_date }}</td>
                                <td>{{ $transaction->payment_method }}</td>
                                <td>{{ $transaction->reference }}</td>
                                <td>{{ $transaction->notes }}</td>

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
                                Menampilkan {{ $transactions->firstItem() }} sampai {{ $transactions->lastItem() }} dari {{ $transactions->total() }} entri
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
    </div>
</div>
@endsection
