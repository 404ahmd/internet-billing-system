@extends('admin.master')

@section('content')

<div class="main-panel">
    <div class="content">
        <div class="card-body">

            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

            <form method="POST" action="{{ route('admin.package.update', $packages->id) }}">

                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Paket</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ $packages->name }}" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea id="description" name="description" class="form-control" value="{{ $packages->description }}"></textarea>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Harga</label>
                    <input type="text" id="price" name="price" class="form-control" value="{{ $packages->price }}" required>
                </div>

                <div class="mb-3">
                    <label for="cycle" class="form-label">Siklus</label>
                    <input type="text" id="cycle" name="cycle" class="form-control" value="{{ $packages->cycle }}" required>
                </div>

                <div class="mb-3">
                    <label for="type" class="form-label">Tipe</label>
                    <select name="type" id="type" class="form-control" required>
                        <option value="pppoe">PPPoE</option>
                        <option value="hotspot">Hotspot</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="bandwidth" class="form-label">Bandwidth</label>
                    <input type="text" id="bandwidth" name="bandwidth" value="{{ $packages->bandwidth }}" class="form-control">
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="available">Tersedia</option>
                        <option value="unavailable">Tidak Tersedia</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Produk</button>
            </form>
        </div>
    </div>
</div>

@endsection
