@extends('admin.master')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="card">
            <div class="card-body">
                <h1>dashboard {{ Auth::user()->role }}</h1>
            </div>
        </div>
    </div>
</div>
@endsection
