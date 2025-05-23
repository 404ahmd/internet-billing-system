@extends('finance.master')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="card">
            <div class="card-body">

                <div class="container mx-auto px-4 py-6">
                    <h2 class="text-2xl font-bold mb-4">Data Pelanggan</h2>

                    <table class="table-auto w-full border mb-6">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="px-4 py-2 border">N0</th>
                                <th class="px-4 py-2 border">Nama</th>
                                <th class="px-4 py-2 border">Username</th>
                                <th class="px-4 py-2 border">Paket</th>
                                <th class="px-4 py-2 border">Group</th>
                                <th class="px-4 py-2 border">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($customers as $index => $customer)
                            <tr>
                                <td class="px-4 py-2 border">{{ $customers->firstItem() + $index }}</td>
                                <td class="px-4 py-2 border">{{ $customer->name }}</td>
                                <td class="px-4 py-2 border">{{ $customer->username }}</td>
                                <td class="px-4 py-2 border">{{ $customer->package }}</td>
                                <td class="px-4 py-2 border">{{ $customer->group ?? '-' }}</td>
                                <td class="px-4 py-2 border">{{ $customer->status }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Tidak ada data pelanggan.</td>
                            </tr>
                            @endforelse
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
@endsection
