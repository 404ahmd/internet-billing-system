<!-- Sidebar -->
<div class="sidebar sidebar-style-2">
    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">
            <ul class="nav nav-primary">
                <li class="nav-item">
                    <a href="{{ route('operator.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <p>Dashboard</p></br>
                        <p> {{ Auth::user()->role }}</p>
                    </a>


                </li>

                <!-- ================== Pelanggan ================== -->
                <li class="nav-section">
                    <span class="sidebar-mini-icon"><i class="fas fa-users"></i></span>
                    <h4 class="text-section">Pelanggan</h4>
                </li>
                <li class="nav-item">
                    <a href="{{ route('operator.customer.view') }}">
                        <i class="fas fa-address-book"></i>
                        <p>Manajemen Pelanggan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('operator.customer.activation') }}">
                        <i class="fas fa-check-circle"></i>
                        <p>Aktivasi Pelanggan</p>
                    </a>
                </li>

                <hr>

                <!-- ================== Jaringan ================== -->
                <li class="nav-section">
                    <span class="sidebar-mini-icon"><i class="fas fa-network-wired"></i></span>
                    <h4 class="text-section">Jaringan</h4>
                </li>
                <li class="nav-item">
                    <a href="{{ route('operator.router.view') }}"">
                        <i class="fas fa-server"></i>
                        <p>Router</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('operator.ip-pool.create') }}">
                        <i class="fas fa-project-diagram"></i>
                        <p>IP Pool</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('operator.ppp-profile.create') }}">
                        <i class="fas fa-network-wired"></i>
                        <p>PPPoE Profile</p>
                    </a>
                </li>

                 <li class="nav-item">
                    <a href="{{route('operator.ppp-secret.create')}}">
                        <i class="fas fa-solid fa-wifi"></i>
                        <p>PPPoE Secret</p>
                    </a>
                </li>

                <hr>

                <!-- ================== Paket ================== -->
                <li class="nav-section">
                    <span class="sidebar-mini-icon"><i class="fas fa-box"></i></span>
                    <h4 class="text-section">Paket</h4>
                </li>
                <li class="nav-item">
                    <a href="{{ route('operator.package.view') }}">
                        <i class="fas fa-cogs"></i>
                        <p>Manajemen Paket</p>
                    </a>
                </li>

                <hr>

                <!-- ================== Laporan ================== -->
                <li class="nav-section">
                    <span class="sidebar-mini-icon"><i class="fas fa-chart-line"></i></span>
                    <h4 class="text-section">Laporan</h4>
                </li>
                <li class="nav-item">
                    <a href="{{ route('operator.invoices.view') }}">
                        <i class="fas fa-file-invoice"></i>
                        <p>Lihat Invoice</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('operator.transaction.view') }}">
                        <i class="fas fa-history"></i>
                        <p>Riwayat Transaksi</p>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>
<!-- End Sidebar -->
