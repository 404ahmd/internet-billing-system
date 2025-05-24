<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function dashboard()
    {
        $username = session('member');

        // Ambil data customer berdasarkan username
        $member = Customer::where('username', $username)->first();

        if (!$member) {
            return redirect()->route('member.login')->with('errors', 'Anda tidak memiliki akses');
        }

        // Ambil invoice dan transaksi milik member dengan pagination
        $invoices = $member->invoices()->latest()->paginate(10, ['*'], 'invoice_page');
        $transactions = $member->transactions()->latest()->paginate(10, ['*'], 'transaction_page');

        return view('member.dashboard', compact('member', 'invoices', 'transactions'));
    }
}
