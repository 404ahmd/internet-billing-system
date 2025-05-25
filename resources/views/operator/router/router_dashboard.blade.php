@extends('operator.master')

@section('content')
    <div class="main-panel">
        <div class="content">
            <div class="card">
                <div class="card-body">
                    <h2>Router MikroTik Monitoring</h2>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    @if ($errors->any())
                        )
                        <div class="alert alert-danger alert-dismissible fade show">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                        </div>
                    @endif

                    <!-- Form Connect -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Tambah Router Baru</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('operator.router.connect') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nama Router</label>
                                            <input type="text" name="name" class="form-control" required
                                                placeholder="Contoh: Router Kantor Pusat">
                                        </div>
                                        <div class="form-group">
                                            <label>IP Address</label>
                                            <input type="text" name="host" class="form-control" required
                                                placeholder="Contoh: 192.168.88.1">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Username</label>
                                            <input type="text" name="username" class="form-control" required
                                                placeholder="Biasanya 'admin'">
                                        </div>
                                        <div class="form-group">
                                            <label>Password</label>
                                            <input type="password" name="password" class="form-control" required
                                                placeholder="Password router">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-3 p-0">
                                    <label>Port API (default: 8728)</label>
                                    <input type="number" name="port" class="form-control" placeholder="8728"
                                        min="1" max="65535">
                                </div>
                                <button type="submit" class="btn btn-primary mt-2">
                                    <i class="fas fa-plug"></i> Connect
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Tabel Router -->
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Daftar Router</h5>
                        </div>
                        <div class="card-body">
                            @if ($routers->isEmpty())
                                <div class="alert alert-info">Belum ada router yang terhubung</div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped" id="router-table">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Nama Router</th>
                                                <th>IP Address</th>
                                                <th>Status</th>
                                                <th>CPU Load</th>
                                                <th>Uptime</th>
                                                <th>Memory Usage</th>
                                                <th>Last Seen</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($routers as $router)
                                                <tr id="router-row-{{ $router->id }}">
                                                    <td>{{ $router->name }}</td>
                                                    <td>{{ $router->host }}:{{ $router->port }}</td>
                                                    <td class="status">
                                                        <span class="badge badge-secondary">Checking...</span>
                                                    </td>
                                                    <td class="cpu_load">-</td>
                                                    <td class="uptime">-</td>
                                                    <td class="memory_usage">-</td>
                                                    <td class="last_seen">
                                                        {{ $router->last_seen_at ? \Carbon\Carbon::parse($router->last_seen_at)->diffForHumans() : 'Never' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Fungsi untuk memformat memory usage
            // function formatMemory(percent) {
            //     return `<div class="progress" style="height: 20px;">
        //                 <div class="progress-bar" role="progressbar" style="width: ${percent}%" 
        //                      aria-valuenow="${percent}" aria-valuemin="0" aria-valuemax="100">
        //                     ${percent}%
        //                 </div>
        //             </div>`;
            // }

            function formatMemory(percent) {
                // Validasi agar percent berada di antara 0 - 100
                let value = parseFloat(percent);
                if (isNaN(value) || value < 0) value = 0;
                if (value > 100) value = 100;

                return `
        <div class="progress" style="height: 20px;">
            <div class="progress-bar" role="progressbar"
                 style="width: ${value}%"
                 aria-valuenow="${value}" aria-valuemin="0" aria-valuemax="100">
                ${value.toFixed(1)}%
            </div>
        </div>
    `;
            }


            // Fungsi refresh status router
            function refreshRouterStatus() {
                @foreach ($routers as $router)
                    $.ajax({
                        url: '{{ route('operator.router.status', $router->id) }}',
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            const row = $('#router-row-{{ $router->id }}');

                            if (response.online) {
                                // Update status online
                                row.find('.status').html(
                                    '<span class="badge badge-success">Online</span>');
                                row.find('.cpu_load').text(response.data.cpu_load + '%');
                                row.find('.uptime').text(response.data.uptime);
                                row.find('.memory_usage').html(formatMemory(response.data
                                    .memory_usage));
                                row.find('.last_seen').text('Just now');

                                // Tambahkan class untuk efek visual
                                row.removeClass('table-danger').addClass('table-success');
                            } else {
                                // Update status offline
                                row.find('.status').html(
                                    '<span class="badge badge-danger">Offline</span>');
                                row.find('.cpu_load').text('-');
                                row.find('.uptime').text('-');
                                row.find('.memory_usage').html('-');
                                row.find('.last_seen').text(response.last_seen || '-');

                                // Tambahkan class untuk efek visual
                                row.removeClass('table-success').addClass('table-danger');
                            }
                        },
                        error: function(xhr) {
                            console.error('Error checking router status:', xhr.responseText);
                        }
                    });
                @endforeach
            }

            // Jalankan pertama kali
            refreshRouterStatus();

            // Set interval untuk auto-refresh setiap 5 detik
            setInterval(refreshRouterStatus, 5000);
        });
    </script>
@endsection
