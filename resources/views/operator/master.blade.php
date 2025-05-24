<!DOCTYPE html>
<html lang="en">

@include('layouts.head')

<body>
	<div class="wrapper">
		<div class="main-header">
			<!-- End Logo Header -->
			@include('layouts.logo_header')
			<!-- End Logo Header -->

            <!-- HEADER -->
            @include('layouts.navbar')
            <!-- HEADER -->
		</div>
        @include('operator.sidebar')
		@yield('content')
	</div>

    @include('layouts.script')

    <!-- Overlay -->
<div class="overlay-sidebar" style="display: none;"></div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.overlay-sidebar');
        const toggleSidebarBtn = document.querySelector('.toggle-sidebar');

        toggleSidebarBtn.addEventListener('click', function () {
            sidebar.classList.toggle('show');
            overlay.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
        });

        // Klik di luar sidebar akan menutup
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('show');
            overlay.style.display = 'none';
        });
    });
</script>

</body>
</html>
