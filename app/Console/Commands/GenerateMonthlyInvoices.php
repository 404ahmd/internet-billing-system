<?php

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMonthlyInvoices extends Command
{
    protected $signature = 'invoices:generate-monthly';
    protected $description = 'Generate new invoices every month for paid customers';

    public function handle()
    {
        $today = Carbon::now();

        // Ambil semua customer yang aktif dan punya invoice terakhir yang paid
        $customers = Customer::where('status', 'active')->get();

        foreach ($customers as $customer) {
            // Ambil invoice terakhir yang paid
            $lastInvoice = Invoice::where('customer_id', $customer->id)
                ->where('status', 'paid')
                ->orderBy('due_date', 'desc')
                ->first();

            // Skip kalau tidak ada invoice paid
            if (!$lastInvoice) continue;

            // Cek apakah sudah ada invoice bulan ini
            $existingInvoice = Invoice::where('customer_id', $customer->id)
                ->whereMonth('issue_date', $today->month)
                ->whereYear('issue_date', $today->year)
                ->first();

            if ($existingInvoice) continue;

            // Generate invoice baru
            $newIssueDate = Carbon::parse($lastInvoice->due_date)->addDay();
            $newDueDate = Carbon::parse($lastInvoice->due_date)->addMonth();

            // Buat nomor invoice
            $date = $newDueDate->format('Ymd');
            $countToday = Invoice::whereDate('due_date', $newDueDate)->count() + 1;
            $invoiceNumber = 'INV-' . $date . '-' . str_pad($countToday, 3, '0', STR_PAD_LEFT);

            // Buat invoice baru
            Invoice::create([
                'customer_id'    => $customer->id,
                'package_id'     => $lastInvoice->package_id,
                'invoice_number' => $invoiceNumber,
                'issue_date'     => $newIssueDate,
                'due_date'       => $newDueDate,
                'amount'         => $lastInvoice->amount,
                'tax_amount'     => $lastInvoice->tax_amount,
                'total_amount'   => $lastInvoice->total_amount,
                'status'         => 'unpaid',
                'notes'          => 'Invoice otomatis untuk bulan ' . $newIssueDate->format('F Y'),
            ]);

            $this->info("Invoice created for customer {$customer->id}");
        }

        return Command::SUCCESS;
    }
}
