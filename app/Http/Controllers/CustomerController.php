<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{

    public function autocomplete(Request $request){
        $search = $request->get('query');

        $results = \App\Models\Customer::where('name', 'like', "%$search%")
        ->select('id', 'name')
        ->limit(10)
        ->get();

        return response()->json($results);
    }
}
