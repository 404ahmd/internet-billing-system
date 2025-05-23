@extends('finance.master')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="card">
            <div class="card-body">
                <h4>Dashboard {{ Auth::user()->role }}</h4>

                {{-- INCOME PER MONTH CHART --}}
                <div class="row mt-4">
                    <div class="col-md-6">
                        <h4 class="mb-3">Laporan Pendapatan Bulanan (6 Bulan Terakhir)</h4>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Bulan</th>
                                    <th>Total Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($monthlyRevenue as $row)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($row->month)->translatedFormat('F Y') }}</td>
                                    <td>Rp{{ number_format($row->total, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6 d-flex align-items-center justify-content-center">
                        <canvas id="revenueChart" style="max-width: 100%; height: 300px;"></canvas>
                    </div>
                </div>

                {{-- PAYMENT STATUS --}}
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header"><strong>Status Pembayaran Pelanggan Dalam Satu Bulan</strong></div>
                            <div class="card-body">
                                <table class="table table-bordered table-sm">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Status</th>
                                            <th>Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><span class="badge bg-success">Lunas</span></td>
                                            <td>{{ $paymentStatus['paid'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-warning text-dark">Belum Lunas</span></td>
                                            <td>{{ $paymentStatus['unpaid'] }}</td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge bg-danger">Terlambat</span></td>
                                            <td>{{ $paymentStatus['overdue'] }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header"><strong>Grafik Status Pembayaran</strong></div>
                            <div class="card-body">
                                <canvas id="paymentChart" style="max-width: 100%; height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TRANSACTIONS CHART --}}
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header"><strong>Tabel Jumlah Transaksi</strong></div>
                            <div class="card-body">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Bulan</th>
                                            <th>Jumlah Transaksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($months as $index => $month)
                                        <tr>
                                            <td>{{ $month }}</td>
                                            <td>{{ $transactionsCount[$index] }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header"><strong>Grafik Jumlah Transaksi (6 Bulan Terakhir)</strong></div>
                            <div class="card-body">
                                <canvas id="transactionChart" style="max-width: 100%; height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{-- INCOME CHART --}}
<script>
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($monthlyRevenue->pluck('month')->map(fn($m) => \Carbon\Carbon::parse($m)->translatedFormat('F Y'))) !!},
        datasets: [{
            label: 'Pendapatan',
            data: {!! json_encode($monthlyRevenue->pluck('total')) !!},
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2,
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: value => 'Rp' + value.toLocaleString('id-ID')
                }
            }
        }
    }
});
</script>

{{-- PAYMENT STATUS CHART --}}
<script>
    const paymentCtx = document.getElementById('paymentChart').getContext('2d');
new Chart(paymentCtx, {
    type: 'pie',
    data: {
        labels: ['Lunas', 'Belum Lunas', 'Terlambat'],
        datasets: [{
            data: [
                {{ $paymentStatus['paid'] }},
                {{ $paymentStatus['unpaid'] }},
                {{ $paymentStatus['overdue'] }}
            ],
            backgroundColor: ['#4CAF50', '#FFC107', '#F44336'],
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.raw || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = Math.round((value / total) * 100);
                        return `${label}: ${value} (${percentage}%)`;
                    }
                }
            }
        }
    }
});
</script>

{{-- TRANSACTION COUNT CHART --}}
<script>
    const transactionCtx = document.getElementById('transactionChart').getContext('2d');
new Chart(transactionCtx, {
    type: 'bar',
    data: {
        labels: @json($months),
        datasets: [{
            label: 'Jumlah Transaksi',
            data: @json($transactionsCount),
            backgroundColor: 'rgba(75, 192, 192, 0.6)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1,
            borderRadius: 5
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
@endsection
