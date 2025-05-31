@extends('admin.master')

@section('content')
    <div class="main-panel">
        <div class="content">
            <div class="card">
                <div class="card-body">
                    <div class="container mx-auto px-4 py-6">
                        <h1 class="text-2xl font-bold mb-6">Daftar Customer Dengan Invoice Belum Dibayar</h1>

                        <div class="bg-white shadow-md rounded-lg overflow-hidden mt-4">

                            <table class="table-auto w-full border mb-6">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 border">No</th>
                                        <th class="px-4 py-2 border">Nama</th>
                                        <th class="px-4 py-2 border">Telepon</th>
                                        <th class="px-4 py-2 border">Jumlah Tagihan Belum Dibayar</th>
                                        <th class="px-4 py-2 border">Jatuh Tempo</th>
                                        <th class="px-4 py-2 border">Total Tagihan</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($invoices as $index => $invoice)
                                        @php
                                            $message =
                                                "Halo {$invoice->customer->name}, tagihan internet Anda sebesar Rp " .
                                                number_format($invoice->total_amount, 0, ',', '.') .
                                                ' akan jatuh tempo pada ' .
                                                \Carbon\Carbon::parse($invoice->due_date)->format('d-m-Y') .
                                                '. Mohon segera melakukan pembayaran. Terima kasih.';
                                            $waLink =
                                                'https://wa.me/' .
                                                $invoice->customer->phone .
                                                '?text=' .
                                                urlencode($message);
                                        @endphp
                                        <tr>
                                            <td class="px-4 py-2 border">{{ $invoices->firstItem() + $index }}</td>
                                            <td class="px-4 py-2 border">{{ $invoice->customer->name }}</td>
                                            <td class="px-4 py-2 border">{{ $invoice->customer->phone }}</td>
                                            <td class="px-4 py-2 border">Rp
                                                {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                                            <td class="px-4 py-2 border">
                                                {{ \Carbon\Carbon::parse($invoice->due_date)->format('d-m-Y') }}</td>
                                            <td class="px-4 py-2 border">Rp
                                                {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                                            <td>
                                                <a href="{{ $waLink }}" target="_blank"
                                                    class="btn btn-success btn-sm">Kirim Pesan</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-4 py-2 text-center text-gray-500 border">Tidak ada
                                                data tagihan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <p class="text-muted">
                                    Menampilkan {{ $invoices->firstItem() }} sampai {{ $invoices->lastItem() }} dari
                                    {{ $invoices->total() }} entri
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
        </div>
    </div>
@endsection
