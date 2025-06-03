@extends('finance.master')
@section('content')
    <div class="main-panel">
        <div class="content">
            <div class="card">
                <div class="card-body">
                    <div class="container-fluid px-4 py-4">

                        {{-- FORM PENCARIAN NAMA CUSTOMER --}}
                        <form method="GET" action="{{ route('finance.invoices.search') }}" class="mb-3">
                            <div class="mb-2">
                                <label for="customer_search_input" class="form-label">Cari berdasarkan nama customer</label>
                                <input type="text" name="customer_name" id="customer_search_input" class="form-control"
                                    value="{{ request('customer_name') }}" placeholder="Masukkan nama customer...">
                            </div>
                            <div id="search_suggestions" class="list-group position-absolute w-100 z-3"
                                style="max-height: 200px; overflow-y: auto;"></div>
                            <button type="submit" class="btn btn-primary btn-sm mt-2">Cari</button>
                        </form>


                        {{-- FORM FILTER STATUS PEMBAYARAN --}}
                        <form method="GET" action="{{ route('finance.invoices.search') }}" class="mb-4">
                            <div class="mb-2">
                                <label for="status" class="form-label">Filter berdasarkan status pembayaran</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">-- Semua Status --</option>
                                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Lunas
                                    </option>
                                    <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Belum
                                        Dibayar
                                    </option>
                                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Terlambat
                                    </option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary">Terapkan</button>
                        </form>

                        <h2 class="text-2xl font-bold mb-4">Laporan Pembuatan Invoice</h2>
                         <a href="{{ route('export.invoices') }}" class="btn btn-sm btn-secondary mt-2 mb-2">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </a>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Pelanggan</th>
                                        <th>Paket</th>
                                        <th>Nomor Invoice</th>
                                        <th>Tanggal Penerbitan</th>
                                        <th>Jatuh Tempo</th>
                                        <th class="text-right">Jumlah</th>
                                        <th class="text-right">Pajak</th>
                                        <th class="text-right">Total</th>
                                        <th>Status</th>
                                        <th>Tanggal Bayar</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($invoices as $index => $invoice)
                                        <tr>
                                            <td class="text-center">{{ $invoices->firstItem() + $index }}</td>
                                            <td>{{ $invoice->customer->name ?? '-' }}</td>
                                            <td>{{ $invoice->package->name ?? '-' }}</td>
                                            <td>{{ $invoice->invoice_number }}</td>
                                            <td>{{ $invoice->issue_date }}</td>
                                            <td>{{ $invoice->due_date }}</td>
                                            <td class="text-right">{{ number_format($invoice->amount, 0, ',', '.') }}</td>
                                            <td class="text-right">{{ number_format($invoice->tax_amount, 0, ',', '.') }}
                                            </td>
                                            <td class="text-right">{{ number_format($invoice->total_amount, 0, ',', '.') }}
                                            </td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $invoice->status == 'paid' ? 'success' : 'warning' }}">
                                                    {{ $invoice->status }}
                                                </span>
                                            </td>
                                            <td>{{ $invoice->paid_at ? $invoice->paid_at->format('d/m/Y') : '-' }}</td>
                                            <td>{{ Str::limit($invoice->notes, 20) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="12" class="text-center">Tidak ada data invoice</td>
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
