@extends('operator.master')
@section('content')
<div class="main-panel">
    <div class="content">
        <div class="card">
            <div class="card-body">

                {{-- ==================================================== --}}

                <div class="row-mt-4">
                    <div class="col">
                        <div class="card h-100">
                            <div class="card-header"><strong>Tabel Jumlah Transaksi</strong></div>
                            <div class="card-body">
                              <h1>Dashboard {{ Auth::user()->role }}</h1>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ==================================================== --}}

            </div>
        </div>
    </div>
</div>


@endsection
