<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Report;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function dashboard()
    {
        $startDate = Carbon::now()->subMonths(8)->startOfMonth();
        $endDate = Carbon::now()->endOfMonth();
        $monthlyRevenue = Transaction::select(
            DB::raw("DATE_FORMAT(payment_date, '%Y-%m') as month"),
            DB::raw("SUM(amount) as total")
        )
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();


        // ==========================================================
        $paymentStatus = [
            'paid' => Invoice::where('status', 'paid')->count(),
            'unpaid' => Invoice::where('status', 'unpaid')->count(),
            'overdue' => Invoice::where('status', 'overdue')->count(),
        ];

        // ==========================================================
        $months = collect();
        $transactionsCount = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthName = $date->format('F Y');

            $count = Transaction::whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->count();

            // $amount = Transaction::whereYear('payment_date', $date->year)
            //     ->whereMonth('payment_date', $date->month)
            //     ->sum('amount') / 1000000;

            $months->push($monthName);
            $transactionsCount->push($count);
            //$transactionsAmount->push(round($amount, 2));
        }

        return view('finance.dashboard', [
            'monthlyRevenue' => $monthlyRevenue,
            'paymentStatus' => $paymentStatus,
            'months' => $months,
            'transactionsCount' => $transactionsCount,
            //'transactionsAmount' => $transactionsAmount,
        ]);
    }


    public function getAllInvoices()
    {
        $invoices = Invoice::latest()->paginate(10);
        return view('finance.list_invoice', [
            'invoices' => $invoices
        ]);
    }


     public function searchUnpaid(Request $request)
    {
        $keyword = $request->input('customer_name');

        $invoices = Invoice::with('customer')
            ->where('status', 'unpaid')
            ->whereHas('customer', function ($query) use ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            })
            ->paginate(10);

        return view('finance.customer_arrears', compact('invoices', 'keyword'));
    }


    public function transactionsHistory()
    {
        $transactions = Transaction::latest()->paginate(10);
        return view('finance.transactions_history', [
            'transactions' => $transactions
        ]);
    }

     public function getCustomerData(){
        $customers = Customer::select('name', 'username', 'package', 'group', 'status')->latest()
        ->paginate(10);

        return view('finance.customer_data', [
            'customers' => $customers,
        ]);
    }

    public function activityReport(){
        $reports = Report::select('source', 'description', 'created_at')
        ->latest()
        ->paginate(10);
        return view('finance.activity_report', [
            'reports' => $reports
        ]);
    }

    public function customerArrears(){
         $invoices = Invoice::with('customer')
        ->where('status', 'unpaid')->paginate(10);

        return view('finance.customer_arrears', [
            'invoices' => $invoices,
        ]);
    }

    public function search(Request $request){
         $request->validate([
            'keyword' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive,terminated,free,other'
        ]);

        $keyword = $request->keyword;
        $status = $request->status;

        $query = DB::table('customers');

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('username', 'like', '%' . $keyword . '%');
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $customers = $query->paginate(10);

        // Ambil kembali data lain yang dibutuhkan view
        $packages = \App\Models\Package::all();
        $invoices = \App\Models\Invoice::all();

        return view('finance.customer_data', [
            'customers' => $customers,
            'packages' => $packages,
            'invoices' => $invoices,
            'keyword' => $keyword,
            'status' => $status,
        ]);
    }

    public function searchInvoice(Request $request){
        $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'status' => 'nullable|in:paid,unpaid,overdue',
        ]);

        $keyword = $request->input('customer_name');
        $status = $request->input('status');

        $invoices = Invoice::with(['customer', 'package'])
            ->when($keyword, function ($query, $keyword) {
                $query->whereHas('customer', function ($q) use ($keyword) {
                    $q->where('name', 'like', '%' . $keyword . '%');
                });
            })
            ->when($status, function ($query, $status) {
                if ($status === 'paid') {
                    $query->where('status', 'paid');
                } elseif ($status === 'unpaid') {
                    $query->where('status', 'unpaid')
                        ->whereDate('due_date', '>=', now());
                } elseif ($status === 'overdue') {
                    $query->where('status', 'unpaid')
                        ->whereDate('due_date', '<', now());
                }
            })
            ->latest()
            ->paginate(10)
            ->appends($request->query()); // agar filter tetap saat paginate

        return view('finance.list_invoice', compact('invoices', 'keyword', 'status'));
    
    }

    public function markAsPaid(Invoice $invoice){
        $invoice->update([
            'status' => 'paid',
            'paid_at' =>now(),
        ]);

        return back()->with('success', 'Sudah lunas');
    }
}
