
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
              @include('finance.sidebar')

		</div>

		@include('layouts.setting')


        <div class="main-content">
            @yield('content')
        </div>
	</div>

    @include('layouts.script')

</body>
</html>
