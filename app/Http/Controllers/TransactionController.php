<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(){
        $transactions = Transaction::all();
        return view('transaction.transactions_customer', [
            'transactions' => $transactions
        ]);
    }

    public function search(Request $request){
        $request->validate([
            'keyword' => 'string|max:255'
        ]);

        $keyword = $request->keyword;
        //$transactions = DB::table('transactions')->where('customer_name', 'like', '%'. $keyword .'%')->paginate();

        $transactions = Transaction::whereHas('customer', function($query) use ($keyword){
            $query->where('name', 'like', '%'. $keyword .'%');
        })->paginate();
        return view('transaction.transactions_customer', compact('transactions'));
    }
}
