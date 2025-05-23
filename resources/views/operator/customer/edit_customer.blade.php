@extends('operator.master')

@section('content')

<div class="main-panel">
    <div class="content">
        <div class="card-body">

            <div class="container">

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

                <h1>{{ isset($customer) ? 'Edit' : 'Create' }} Customer</h1>

                <form action="{{ isset($customer) ? route('operator.customer.update', $customer->id) : route('operator.customers.store') }}" method="POST">
                    @csrf
                    @if(isset($customer))
                        @method('PUT')
                    @endif

                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $customer->name ?? '') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="{{ old('username', $customer->username ?? '') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="package" class="form-label">Paket</label>
                        <input type="text" class="form-control" id="package" name="package" value="{{ old('paket', $customer->package ?? '') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" required>{{ old('address', $customer->address ?? '') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="group" class="form-label">Group (optional)</label>
                        <input type="text" class="form-control" id="group" name="group" value="{{ old('group', $customer->group ?? '') }}">
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone', $customer->phone ?? '') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="join_date" class="form-label">Join Date</label>
                        <input type="date" class="form-control" id="join_date" name="join_date"
                               value="{{ old('join_date', isset($customer) && $customer->join_date ? \Carbon\Carbon::parse($customer->join_date)->format('Y-m-d') : '') }}"
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active" {{ old('status', $customer->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $customer->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="terminated" {{ old('status', $customer->status ?? '') == 'terminated' ? 'selected' : '' }}>Terminated</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="last_payment_date" class="form-label">Last Payment Date (optional)</label>
                        <input type="date" class="form-control" id="last_payment_date" name="last_payment_date"
                               value="{{ old('last_payment_date', isset($customer) && $customer->last_payment_date ? \Carbon\Carbon::parse($customer->last_payment_date)->format('Y-m-d') : '') }}">
                    </div>

                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date (optional)</label>
                        <input type="date" class="form-control" id="due_date" name="due_date"
                               value="{{ old('due_date', isset($customer) && $customer->due_date ? \Carbon\Carbon::parse($customer->due_date)->format('Y-m-d') : '') }}">
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes (optional)</label>
                        <textarea class="form-control" id="notes" name="notes">{{ old('notes', $customer->notes ?? '') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ route('operator.customer.view') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>

        </div>
    </div>
</div>


@endsection
