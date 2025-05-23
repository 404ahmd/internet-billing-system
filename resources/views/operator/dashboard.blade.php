@extends('layouts.master')
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
                                {{-- <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Bulan</th>
                                            <th>Jumlah Transaksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($activeCustomers as $row)
                                            <tr>
                                            <td>{{ $row['date'] }}</td>
                                            <td>{{ $row['active_count'] }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table> --}}
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
