@extends('operator.master')

@section('content')
    <div class="main-panel">
        <div class="content">
            <div class="card">
                <div class="card-body">
                    <h2>Dashboard Router: <span id="router-name">{{ $router->name }}</span></h2>

                    <div class="row">
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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
