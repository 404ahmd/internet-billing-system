<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Package;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
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
