<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Package;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ActivationController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        $packages = Package::all();
        return view('customer.activation-customer', compact(['customers', 'packages']));
    }

    public function store(Request $request)
    {
        // Validasi request (tanpa invoice_number karena kita generate sendiri)
        $validated = $request->validate([
            'customer_id'   => 'required|exists:customers,id',
            'package_id'    => 'required|exists:packages,id',
            'issue_date'    => 'required|date',
            'due_date'      => 'required|date',
            'amount'        => 'required|numeric',
            'tax_amount'    => 'nullable|numeric',
            'total_amount'  => 'required|numeric',
            'status'        => 'required|in:paid,unpaid,overdue',
            'paid_at'       => 'nullable|date',
            'notes'         => 'nullable|string',
        ]);

        try {
            // Generate nomor invoice otomatis
            $date = Carbon::parse($validated['due_date'])->format('Ymd');
            $countToday = Invoice::whereDate('due_date', $validated['due_date'])->count() + 1;
            $invoiceNumber = 'INV-' . $date . '-' . str_pad($countToday, 3, '0', STR_PAD_LEFT);

            // Jika status paid tapi paid_at kosong, isi otomatis
            if ($validated['status'] === 'paid' && empty($validated['paid_at'])) {
                $validated['paid_at'] = now();
            }

            // Simpan invoice
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'customer_id'    => $validated['customer_id'],
                'package_id'     => $validated['package_id'],
                'issue_date'     => $validated['issue_date'],
                'due_date'       => $validated['due_date'],
                'amount'         => $validated['amount'],
                'tax_amount'     => $validated['tax_amount'] ?? 0,
                'total_amount'   => $validated['total_amount'],
                'status'         => $validated['status'],
                'paid_at'        => $validated['paid_at'],
                'notes'          => $validated['notes'],
            ]);

            // Update due_date ke customer
            Customer::where('id', $validated['customer_id'])->update([
                'due_date' => $validated['due_date'],
                'package' => $validated['package_id']
            ]);

            // Jika status paid, buat transaksi otomatis
            if ($validated['status'] === 'paid') {
                Transaction::firstOrCreate([
                    'invoice_id'     => $invoice->id,
                    'customer_id'    => $validated['customer_id'],
                    'amount'         => $validated['amount'],
                    'payment_date'   => $validated['paid_at'],
                    'payment_method' => 'Cash',
                    'reference'      => '',
                    'notes'          => 'Pembayaran invoice nomor ' . $invoiceNumber,
                ]);
            }

            return redirect()->back()->with('success', 'Invoice berhasil disimpan & pelanggan diaktivasi.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyimpan invoice: ' . $e->getMessage())
                ->withInput();
        }
    }
}
