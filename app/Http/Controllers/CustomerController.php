<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
     //function for get customer table view
    public function indexAdmin()
    {
        $customers = Customer::latest()->paginate(10);
        $invoices = Invoice::all();
        $packages = Package::all();
        return view('customer.customer_management', [
            'invoices' => $invoices,
            'customers' => $customers,
            'packages' => $packages
        ]);
    }

    //function for add customer adn send to database
    public function store(Request $request)
    {
        // validation in form

        $validated = $request->validate([
            'name' => 'required',
            'username' => 'required|unique:customers,username',
            'phone' => 'required|digits_between:10,15',
            'address' => 'required',
            'package' => 'required',
            'group' => 'required',
            'join_date' => 'required',
            'status' => 'required',
            'notes' => 'required'
        ]);

        try {
            Customer::create($validated);
            return redirect()->route('customer.view')->with('success', 'data berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'gagal menyimpan data' . $e->getMessage())->withInput();
        }
        //while success, redirect to form and throwing success message
    }

    //function for delete customer by id
    //this function is aplied in delete button
    public function destroy($id)
    {
        $customer_id = Customer::findOrFail($id);
        //check if customer id is empty or cant found
        //redirect back to table and show error message
        if (!$customer_id) {
            return redirect()->route('customer.view')->with('error', 'data tidak bisa dihapus');
        }

        //if the customer id found in database
        //redirec with success message
        $customer_id->delete();
        return redirect()->route('customer.view')->with('success', 'data berhasil dihapus');
    }

    public function edit(Customer $customer)
    {
        return view('customer.edit_customer', [
            'customer' => $customer,
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'username' => 'string|max:255|unique:customers,username,' . $customer->id,
            'package' => 'string|max:255',
            'address' => 'string',
            'group' => 'nullable|string|max:255',
            'phone' => 'string|max:20',
            'join_date' => 'date',
            'status' => 'in:active,inactive,terminated',
            'last_payment_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        try {
           $customer->update($validated);
            return redirect()->route('customer.view')->with('success', 'Customer updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal merubah data pelanggan: ' . $e->getMessage())->withInput();
        }

    }

    public function search(Request $request)
    {
        $request->validate([
            'keyword' => 'string|max:255'
        ]);

        $keyword = $request->keyword;
        $customers = DB::table('customers')->where('name', 'like', '%' . $keyword . '%')->paginate();
        return view('customer.customer_result', compact('customers'));
    }

    public function autocomplete(Request $request){
        $search = $request->get('query');

        $results = \App\Models\Customer::where('name', 'like', "%$search%")
        ->select('id', 'name')
        ->limit(10)
        ->get();

        return response()->json($results);
    }
}
