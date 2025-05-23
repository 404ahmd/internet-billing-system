@extends('operator.master')
@section('content')

<div class="main-panel">
    <div class="content">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive mt-3" style="max-height: 600px;">
                    <table class="table table-bordered table-hover shadow-sm" style="min-width: 900px;">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Telpon</th>
                                <th>Alamat</th>
                                <th>Paket</th>
                                <th>Grup</th>
                                <th>Tanggal Daftar</th>
                                <th>Status</th>
                                <th>Tanggal Jatuh Tempo</th>
                                <th>Catatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($customers as $index => $customer)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="text-primary">{{ $customer->name }}</td>
                                <td>{{ $customer->username }}</td>
                                <td>{{ $customer->phone }}</td>
                                <td>{{ $customer->address }}</td>
                                <td>{{ $customer->package }}</td>
                                <td>{{ $customer->group }}</td>
                                <td>{{ $customer->join_date }}</td>
                                <td>{{ $customer->status }}</td>
                                <td>{{ $customer->due_date }}</td>
                                <td>{{ $customer->notes }}</td>
                                <td>
                                    <a href="{{ route('customer.edit', $customer->id) }}"
                                        class="btn btn-sm btn-warning">Edit</a>
                                    <form action="{{ route('customer.destroy', $customer->id) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Yakin ingin hapus?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="12" class="text-center text-muted">No customers found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
