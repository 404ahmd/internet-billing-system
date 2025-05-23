<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Package;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::latest()->paginate(10);
        return view('invoice.invoice_customer', [
            'invoices' => $invoices,
        ]);
    }

    // public function search(Request $request)
    // {
    //     $request->validate([
    //         'keyword' => 'string|max:255'
    //     ]);

    //     $keyword = $request->keyword;
    //     $invoices = Invoice::whereHas('customer', function ($query) use ($keyword) {
    //         $query->where('name', 'like', '%' . $keyword . '%');
    //     })->with('customer', 'package')->get();
    //     return view('invoice.invoice_customer', [
    //         'invoices' => $invoices,

    //     ]);
    // }

    public function search(Request $request)
    {
        $keyword = $request->input('customer_name');

        $invoices = Invoice::with('customer')
            ->whereHas('customer', function ($query) use ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            })
            ->get();

        return view('invoice.invoice_customer', compact('invoices', 'keyword'));
    }

    //get form invoice update
    public function edit(Invoice $invoice)
    {
        $customers = Customer::all();
        $packages = Package::all();
        return view('invoice.update_invoice', [
            'invoice' => $invoice,
            'packages' => $packages,
            'customers' => $customers
        ]);
    }

    public function update(Request $request, Invoice $invoice)
    {

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'package_id' => 'required|exists:packages,id',
            'invoice_number' => 'required|unique:invoices,invoice_number,' . $invoice->id,
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'amount' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:paid,unpaid,overdue',
            'paid_at' => 'nullable|date', //$request->status == 'paid' ? 'required|date' :
            'notes' => 'nullable|string',
        ]);

        if ($validated['status'] === 'paid' && empty($validated['paid_at'])) {
            $validated['paid_at'] = now();
        } elseif ($validated['status'] !== 'paid') {
            $validated['paid_at'] = null;
        }

        try {
            $invoice->update($validated);
            return redirect()->route('invoice.view')->with('success', 'data berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'gagal mengupdate data' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Invoice $invoice)
    {
        try {
            $invoice->delete();
            return redirect()->route('invoice.view')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal Menghapus Data' . $e->getMessage());
        }
    }

    public function autocomplete(Request $request)
    {
        $query = $request->get('query');

        $customers = Customer::where('name', 'like', '%' . $query . '%')->limit(10)->get();

        return response()->json($customers->map(function ($customer) {
            return [
                'id' => $customer->id,
                'name' => $customer->name
            ];
        }));
    }
}
