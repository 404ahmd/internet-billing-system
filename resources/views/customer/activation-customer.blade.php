@extends('operator.master')
@section('content')
<div class="main-panel">
    <div class="content">
        <div class="card">
            <div class="row">
                <div class="col">
                     <div class="card-header">
                <h4 class="card-title">Form Aktivasi Pelanggan</h4>
            </div>
            <div class="card-body">

                @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">X</button>
                </div>
                @endif

                @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">X</button>
                </div>
                @endif


                <form action="{{ route('admin.customer.activation.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="customer_search" class="form-label">Cari Pelanggan</label>
                        <input type="text" id="customer_search" class="form-control"
                            placeholder="Ketik nama pelanggan..." autocomplete="off">
                        <input type="hidden" name="customer_id" id="customer_id" required>
                        <div id="customer_list" class="list-group mt-1" style="position:absolute; z-index:9999;"></div>
                    </div>


                    <div class="mb-4">
                        <label for="package_id" class="form-label">Pilih Paket</label>
                        <select name="package_id" id="package_id" class="form-select" required>
                            @foreach ($packages as $package)
                            <option value="{{ $package->id }}">{{ $package->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- <div class="mb-4">
                        <label for="invoice_number" class="form-label">Nomor Invoice</label>
                        <input type="text" class="form-control" id="invoice_number" name="invoice_number" required>
                    </div> --}}

                    <div class="mb-4">
                        <label for="issue_date" class="form-label">Tanggal Penerbitan</label>
                        <input type="date" class="form-control" id="issue_date" name="issue_date" required
                            style="max-width:20%;">
                    </div>

                    <div class="mb-4">
                        <label for="due_date" class="form-label">Tanggal Jatuh Tempo</label>
                        <input type="date" class="form-control" id="due_date" name="due_date" required
                            style="max-width: 20%;">
                    </div>

                    <div class="mb-4">
                        <label for="amount" class="form-label">Jumlah</label>
                        <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
                    </div>

                    <div class="mb-4">
                        <label for="tax_amount" class="form-label">Jumlah Pajak</label>
                        <input type="number" step="0.01" class="form-control" id="tax_amount" name="tax_amount"
                            value="0">
                    </div>

                    <div class="mb-4">
                        <label for="total_amount" class="form-label">Jumlah Total</label>
                        <input type="number" step="0.01" class="form-control" id="total_amount" name="total_amount"
                            required>
                    </div>

                    <div class="mb-4">
                        <label for="status" class="form-label">Status Pembayaran</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="paid">Lunas</option>
                            <option value="unpaid" selected>Belum Lunas</option>
                            <option value="overdue">Terlambat</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="paid_at" class="form-label">Tanggal Pembayaran</label>
                        <input type="datetime-local" class="form-control" id="paid_at" name="paid_at"
                            style="max-width: 20%">
                    </div>

                    @push('scripts')
                    <script>
                        document.getElementById('status').addEventListener('change', function() {
                                    const paidAtContainer = document.getElementById('paid_at_container');
                                    if (this.value === 'paid') {
                                        paidAtContainer.style.display = 'block';
                                    } else {
                                        paidAtContainer.style.display = 'none';
                                    }
                                });
                    </script>
                    @endpush

                    <div class="mb-4">
                        <label for="notes" class="form-label">Catatan</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-success">Kirim</button>
                    </div>

                </form>

            </div>
                </div>
            </div>

        </div>
    </div>
</div>

@include('layouts.footer')

{{-- AUTO COMPLETE FOR ACTIVATION CUSTOMER --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('customer_search');
    const list = document.getElementById('customer_list');
    const hiddenId = document.getElementById('customer_id');

    input.addEventListener('input', function () {
        const query = input.value;
        if (query.length < 2) {
            list.innerHTML = '';
            return;
        }

        fetch(`/autocomplete/customer?query=${query}`)
            .then(res => res.json())
            .then(data => {
                list.innerHTML = '';
                data.forEach(customer => {
                    const item = document.createElement('a');
                    item.href = '#';
                    item.classList.add('list-group-item', 'list-group-item-action');
                    item.textContent = customer.name;
                    item.dataset.id = customer.id;

                    item.addEventListener('click', function (e) {
                        e.preventDefault();
                        input.value = customer.name;
                        hiddenId.value = customer.id;
                        list.innerHTML = '';
                    });

                    list.appendChild(item);
                });
            });
    });

    // Tutup list jika klik di luar
    document.addEventListener('click', function (e) {
        if (!list.contains(e.target) && e.target !== input) {
            list.innerHTML = '';
        }
    });
});
</script>
@endsection
