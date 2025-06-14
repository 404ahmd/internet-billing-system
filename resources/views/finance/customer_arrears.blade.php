@extends('finance.master')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="card">
            <div class="card-body">
                <div class="container mx-auto px-4 py-6">

                    {{-- FORM PENCARIAN NAMA CUSTOMER --}}
                    <form method="GET" action="{{ route('finance.invoices.searchUnpaid') }}" class="mb-4">
                        <div class="mb-3 position-relative">
                            <input type="text" name="customer_name" id="customer_search_input" class="form-control"
                                placeholder="Cari berdasarkan nama customer..." value="{{ request('customer_name') }}">
                            <div id="search_suggestions" class="list-group position-absolute w-100 z-3"
                                style="max-height: 200px; overflow-y: auto;"></div>
                        </div>
                        <button type="submit" class="btn btn-primary">Cari</button>
                    </form>

                    <h1 class="text-2xl font-bold mb-6">Daftar Customer Dengan Invoice Belum Dibayar</h1>

                    <div class="bg-white shadow-md rounded-lg overflow-hidden mt-4">

                        <div class="table-responsive">
                            <table class="table-auto w-full border mb-6">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 border">No</th>
                                        <th class="px-4 py-2 border">Nama</th>
                                        <th class="px-4 py-2 border">Telepon</th>
                                        <th class="px-4 py-2 border">Jumlah Tagihan Belum Dibayar</th>
                                        <th class="px-4 py-2 border">Jatuh Tempo</th>
                                        <th class="px-4 py-2 border">Total Tagihan</th>
                                        <th class="px-4 py-2 border">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($invoices as $index => $invoice)
                                    @php
                                    $formatPhoneForWhatsApp = function($phone) {
                                    $phone = preg_replace('/[^0-9]/', '', $phone);
                                    if (substr($phone, 0, 1) === '0') {
                                    $phone = '62' . substr($phone, 1);
                                    }
                                    return $phone;
                                    };

                                    $message = "Halo {$invoice->customer->name},\n\n" .
                                    "Terima kasih telah menggunakan layanan King Net.\n\n" .
                                    "Tagihan internet bulanan Anda:\n" .
                                    "➤ Nominal: Rp " . number_format($invoice->total_amount, 0, ',', '.') . "\n" .
                                    "➤ Jatuh Tempo: " . \Carbon\Carbon::parse($invoice->due_date)->format('d F Y') .
                                    "\n\n" .
                                    "Mohon lakukan pembayaran sebelum jatuh tempo untuk menghindari gangguan layanan.\n\n" .
                                    "Pembayaran dapat dilakukan melalui:\n" .
                                    "1. DANA : 0811278139\n" .
                                    "2. BNI : 0265520960\n" .
                                    "3. BJB : 0126675410100\n" .
                                    "4. OVO : 081389867474\n" .
                                    "A/N JAJULI ADITYA\n\n" .
                                     "Silahkan login melalui tautan berikut : \n".
                                    "Login menggunakan username '{$invoice->customer->username}' \n\n".
                                    "Hormat kami,\n" .
                                    "King Net\n" .
                                    "Layanan Pelanggan";

                                    $phoneForWa = $formatPhoneForWhatsApp($invoice->customer->phone);

                                    $waLink = 'https://wa.me/' . $phoneForWa . '?text=' . urlencode($message);

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
                                            <a href="{{ $waLink }}" target="_blank" class="btn btn-success btn-sm">Kirim
                                                Pesan</a>

                                            <form action="{{ route('finance.invoice.markAsPaid', $invoice->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-primary btn-sm"
                                                    onclick="return confirm('Tandai invoice ini sebagai lunas?')">Lunas</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-2 text-center text-gray-500 border">Tidak
                                            ada
                                            data tagihan.</td>
                                    </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>



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
