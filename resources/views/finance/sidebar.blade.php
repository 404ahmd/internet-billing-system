<!-- Sidebar -->
<div class="sidebar sidebar-style-2">

    <div class="sidebar-wrapper scrollbar scrollbar-inner">
        <div class="sidebar-content">

            <ul class="nav nav-primary">

                <!-- ================== Dashboard ================== -->
                <li class="nav-item">
                    <a href="{{ route('dashboard.finance') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- ================== Laporan ================== -->
                <li class="nav-section">
                    <span class="sidebar-mini-icon"><i class="fas fa-chart-line"></i></span>
                    <h4 class="text-section">Laporan</h4>
                </li>
                <li class="nav-item">
                    <a href={{ route('finance.report') }}>
                        <i class="fas fa-calendar-alt"></i>
                        <p>Laporan Aktivitas</p>
                    </a>
                </li>

                <!-- ================== Keuangan ================== -->
                <li class="nav-section">
                    <span class="sidebar-mini-icon"><i class="fas fa-wallet"></i></span>
                    <h4 class="text-section">Keuangan</h4>
                </li>
                <li class="nav-item">
                    <a href="{{ route('finance.list.customer') }}">
                        <i class="fas fa-file-invoice"></i>
                        <p>Lihat Invoice</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('finance.transactions.history') }}">
                        <i class="fas fa-history"></i>
                        <p>Riwayat Transaksi</p>
                    </a>
                </li>

                <!-- ================== Pelanggan ================== -->
                <li class="nav-section">
                    <span class="sidebar-mini-icon"><i class="fas fa-users"></i></span>
                    <h4 class="text-section">Pelanggan</h4>
                </li>
                <li class="nav-item">
                    <a href={{ route('finance.customer.arrears') }}>
                        <i class="fas fa-user-clock"></i>
                        <p>Tunggakan Pelanggan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href={{ route('finance.data.customers') }}>
                        <i class="fas fa-address-book"></i>
                        <p>Data Pelanggan</p>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>
<!-- End Sidebar -->

