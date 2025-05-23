@extends('admin.master')

@section('content')

<div class="main-panel">
    <div class="content">
        <div class="card">
            <div class="card-body">
                <div class="container mx-auto px-4 py-6">
                    <h1 class="text-2xl font-bold mb-6">Daftar Customer dengan Invoice Belum Dibayar</h1>

                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        <table class="table-auto w-full border mb-6">
                            <thead class="bg-gray-200">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        No</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nama Customer</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Jumlah Invoice Unpaid</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Tagihan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($customers as $index => $customer)
                                <tr>
                                    <td class="px-4 py-2 border">{{ $customers->firstItem() + $index }}</td>
                                    <td class="px-4 py-2 border">{{ $customer->name }}</td>
                                    <td class="px-4 py-2 border">{{ $customer->invoices->count() }}</td>
                                    <td class="px-4 py-2 border">Rp {{
                                        number_format($customer->invoices->sum('total_amount'), 0, ',', '.') }}</td>
                                    {{-- <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('arrears.invoices', $arrears->id) }}"
                                            class="text-blue-600 hover:text-blue-900">Detail</a>
                                    </td> --}}
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt-4">
                    <div class="col-md-6">
                        <p class="text-muted">
                            Menampilkan {{ $customers->firstItem() }} sampai {{ $customers->lastItem() }} dari {{
                            $customers->total() }} entri
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
</div>

@endsection
