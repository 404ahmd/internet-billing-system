
<!DOCTYPE html>
<html lang="en">

@include('layouts.head')

<body>
	<div class="wrapper">
		<div class="main-header">
			<!-- Logo Header -->
			@include('layouts.logo_header')
			<!-- End Logo Header -->

            <!-- HEADER -->
            @include('layouts.navbar')
            <!-- HEADER -->



		</div>
        @include('admin.sidebar')

        @yield('content')

		{{-- @include('layouts.setting') --}}
	</div>

    @include('layouts.script')

</body>
</html>
