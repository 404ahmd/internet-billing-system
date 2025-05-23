@extends('operator.master')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="card-body">

            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3>Edit Invoice {{ $invoice->customer->name }}</h3>
                </div>

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
                    <form action="{{ route('operator.invoices.update', $invoice->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <select name="customer_id" id="customer_id" class="form-select" required>
                                    @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ $invoice->customer_id == $customer->id ?
                                        'selected' : '' }}>
                                        {{ $customer->name }} ({{ $customer->username }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="package_id" class="form-label">Package</label>
                                <select name="package_id" id="package_id" class="form-select" required>
                                    @foreach($packages as $package)
                                    <option value="{{ $package->id }}" {{ $invoice->package_id == $package->id ?
                                        'selected' : '' }}>
                                        {{ $package->name }} (Rp {{ number_format($package->price, 0, ',', '.') }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="invoice_number" class="form-label">Invoice Number</label>
                                <input type="text" class="form-control" id="invoice_number" name="invoice_number"
                                    value="{{ $invoice->invoice_number }}" required>
                            </div>

                            <div class="col-md-4">
                                <label for="issue_date" class="form-label">Issue Date</label>
                                <input type="date" class="form-control" id="issue_date" name="issue_date"
                                    value="{{ $invoice->issue_date }}" required>
                            </div>

                            <div class="col-md-4">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="date" class="form-control" id="due_date" name="due_date"
                                    value="{{ $invoice->due_date }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="amount" class="form-label">Amount</label>
                                <input type="number" step="0.01" class="form-control" id="amount" name="amount"
                                    value="{{ $invoice->amount }}" required>
                            </div>

                            <div class="col-md-4">
                                <label for="tax_amount" class="form-label">Tax Amount</label>
                                <input type="number" step="0.01" class="form-control" id="tax_amount" name="tax_amount"
                                    value="{{ $invoice->tax_amount }}">
                            </div>

                            <div class="col-md-4">
                                <label for="total_amount" class="form-label">Total Amount</label>
                                <input type="number" step="0.01" class="form-control" id="total_amount"
                                    name="total_amount" value="{{ $invoice->total_amount }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="unpaid" {{ $invoice->status == 'unpaid' ? 'selected' : ''
                                        }}>Unpaid</option>
                                    <option value="paid" {{ $invoice->status == 'paid' ? 'selected' : '' }}>Paid
                                    </option>
                                    <option value="overdue" {{ $invoice->status == 'overdue' ? 'selected' : ''
                                        }}>Overdue</option>
                                </select>
                            </div>

                            <div class="col-md-6" id="paid_at_container">
                                <label for="paid_at" class="form-label">Payment Date</label>
                                <input type="datetime-local" class="form-control" id="paid_at" name="paid_at"
                                    value="{{ $invoice->paid_at ? $invoice->paid_at->format('Y-m-d\TH:i') : ''}}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" class="form-control"
                                rows="3">{{ $invoice->notes }}</textarea>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('operator.invoices.view') }}" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Invoice</button>
                        </div>
                    </form>
                </div>
            </div>


            @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const statusSelect = document.getElementById('status');
                    const paidAtContainer = document.getElementById('paid_at_container');

                    function togglePaidAtField() {
                        if (statusSelect.value === 'paid') {
                            paidAtContainer.style.display = 'block';

                            // Auto-fill current datetime if empty
                            if (!document.getElementById('paid_at').value) {
                                const now = new Date();
                                const localDateTime = now.toISOString().slice(0, 16);
                                document.getElementById('paid_at').value = localDateTime;
                            }
                        } else {
                            paidAtContainer.style.display = 'none';
                        }
                    }

                    // Initial toggle based on current status
                    togglePaidAtField();

                    // Add event listener for status change
                    statusSelect.addEventListener('change', togglePaidAtField);
                });
            </script>
            @endpush



        </div>
    </div>
</div>
@endsection
