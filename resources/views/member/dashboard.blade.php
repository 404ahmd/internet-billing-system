<h3>Tagihan Anda</h3>
<table class="table">
    <thead>
        <tr>
            <th>No</th>
            <th>No. Invoice</th>
            <th>Tanggal Terbit</th>
            <th>Jatuh Tempo</th>
            <th>Total</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($invoices as $index => $invoice)
        <tr>
            <td>{{ $invoices->firstItem() + $index }}</td>
            <td>{{ $invoice->invoice_number }}</td>
            <td>{{ $invoice->issue_date }}</td>
            <td>{{ $invoice->due_date }}</td>
            <td>Rp{{ number_format($invoice->total_amount, 2, ',', '.') }}</td>
            <td>{{ ucfirst($invoice->status) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center py-4">Tidak ada data tagihan.</td>
        </tr>
        @endforelse
    </tbody>
</table>
{{ $invoices->links() }} {{-- Pagination --}}

<hr>

<h3>Transaksi Pembayaran</h3>
<table class="table">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Metode</th>
            <th>Jumlah</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($transactions as $index => $tx)
        <tr>
            <td>{{ $transactions->firstItem() + $index }}</td>
            <td>{{ $tx->payment_date }}</td>
            <td>{{ $tx->payment_method }}</td>
            <td>Rp{{ number_format($tx->amount, 2, ',', '.') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center py-4">Belum ada transaksi.</td>
        </tr>
        @endforelse
    </tbody>
</table>
{{ $transactions->links() }} {{-- Pagination --}}
