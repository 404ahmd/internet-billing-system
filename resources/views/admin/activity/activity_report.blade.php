@extends('admin.master')

@section('content')

<div class="main-panel">
    <div class="content">
        <div class="card">
            <div class="card-body">
                <div class="container mx-auto px-4 py-4">

                    <h2 class="text-2xl font-bold mb-4">Laporan Aktivitas Harian</h2>

                    <table class="table-auto w-full border mb-6">
                        <thead class="bg-gray-200">
                            <tr>
                                <th class="px-4 py-2 border">N0</th>
                                <th class="px-4 py-2 border">Sumber</th>
                                <th class="px-4 py-2 border">Deskripsi</th>
                                <th class="px-4 py-2 border">Tanggal & Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reports as $index => $report)
                            <tr>
                                <td class="px-4 py-2 border">{{ $reports->firstItem() + $index }}</td>
                                <td class="px-4 py-2 border">{{ $report->source }}</td>
                                <td class="px-4 py-2 border">{{ $report->description}}</td>
                                <td class="px-4 py-2 border">{{ $report->created_at}}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Tidak ada data aktivitas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <p class="text-muted">
                            Menampilkan {{ $reports->firstItem() }} sampai {{ $reports->lastItem() }} dari {{
                            $reports->total() }} entri
                        </p>
                    </div>

                    <div class="col-md-6">
                        <div class="float-right">
                            {{ $reports->links('vendor.pagination.bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
