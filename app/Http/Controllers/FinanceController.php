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
        $customers = Customer::whereHas('invoices', function($query){
            $query->where('status', 'unpaid');
        })
        ->with(['invoices' => function($query){
            $query->where('status', 'unpaid')
            ->orderBy('due_date', 'asc');
        }])->paginate(10);

        return view('finance.customer_arrears', [
            'customers' => $customers,
        ]);
    }
}
