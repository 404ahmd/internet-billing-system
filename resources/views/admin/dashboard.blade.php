@extends('admin.master')

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

                {{-- ROUTER STATS --}}
                  <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">Informasi Mikrotik</div>
                                <div class="card-body">
                                    <p><strong>Uptime:</strong> <span id="uptime">Memuat...</span></p>
                                    <p><strong>CPU Load:</strong> <span id="cpu-load">Memuat...</span>%</p>
                                    <p><strong>Memory Usage:</strong> <span id="memory-usage">Memuat...</span>%</p>
                                    <p><strong>Versi:</strong> <span id="version">Memuat...</span></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">Grafik CPU & Memori</div>
                                <div class="card-body">
                                    <canvas id="cpuChart" height="150"></canvas>
                                    <canvas id="memoryChart" height="150" class="mt-3"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="error-message" class="alert alert-danger d-none">
                        Gagal mengambil data dari Mikrotik.
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

{{-- ROUTER STATS --}}
 <script>
        const cpuCtx = document.getElementById('cpuChart').getContext('2d');
        const memoryCtx = document.getElementById('memoryChart').getContext('2d');

        const cpuChart = new Chart(cpuCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'CPU Load (%)',
                    data: [],
                    borderColor: 'red',
                    fill: false
                }]
            },
            options: {
                animation: false,
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });

        const memoryChart = new Chart(memoryCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Memory Usage (%)',
                    data: [],
                    borderColor: 'blue',
                    fill: false
                }]
            },
            options: {
                animation: false,
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });

        function fetchStats() {
            fetch("{{ url('/operator/mikrotik/stats') }}")
                .then(res => res.json())
                .then(res => {
                    if (!res.online) {
                        document.getElementById('error-message').classList.remove('d-none');
                        return;
                    }

                    const now = new Date().toLocaleTimeString();
                    const data = res.data;

                    document.getElementById('uptime').textContent = data.uptime;
                    document.getElementById('cpu-load').textContent = data.cpu_load;
                    document.getElementById('version').textContent = data.version;
                    document.getElementById('memory-usage').textContent = data.memory_usage;

                    // Update CPU Chart
                    if (cpuChart.data.labels.length >= 10) cpuChart.data.labels.shift();
                    if (cpuChart.data.datasets[0].data.length >= 10) cpuChart.data.datasets[0].data.shift();
                    cpuChart.data.labels.push(now);
                    cpuChart.data.datasets[0].data.push(parseFloat(data.cpu_load));
                    cpuChart.update();

                    // Update Memory Chart
                    if (memoryChart.data.labels.length >= 10) memoryChart.data.labels.shift();
                    if (memoryChart.data.datasets[0].data.length >= 10) memoryChart.data.datasets[0].data.shift();
                    memoryChart.data.labels.push(now);

                    memoryChart.data.datasets[0].data.push(parseFloat(data.memory_usage));
                    memoryChart.update();

                    document.getElementById('error-message').classList.add('d-none');
                })
                .catch(err => {
                    console.error(err);
                    document.getElementById('error-message').classList.remove('d-none');
                });
        }

        setInterval(fetchStats, 3000);
        fetchStats();
    </script>
@endsection
