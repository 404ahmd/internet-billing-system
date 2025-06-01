<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DummyInvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Package amounts mapping
        $packageAmounts = [
            1 => 150000,
            2 => 200000,
            3 => 250000,
            4 => 300000
        ];

        // Payment methods
        $paymentMethods = [
            'Transfer Bank BCA',
            'Transfer Bank Mandiri',
            'Kartu Kredit',
            'Virtual Account',
            'E-Wallet'
        ];

        $paymemtStatus = [
            'paid',
            'unpaid',
            'overdue'
        ];

        // Generate invoices for Jan, Feb, Mar 2025
        $invoices = [];
        $invoiceCount = 1;

        foreach ([1, 2, 3, 4, 5, 6] as $month) { // Jan, Feb, Mar
            $daysInMonth = Carbon::create(2025, $month)->daysInMonth;

            // Generate 16-17 invoices per month
            for ($i = 0; $i <= 300; $i++) {
                $packageId = rand(1, 4);
                $amount = $packageAmounts[$packageId];
                $taxAmount = $amount * 0.11; // PPN 11%
                $totalAmount = $amount + $taxAmount;

                // Random day in month (avoid weekends if needed)
                $issueDate = Carbon::create(2025, $month, rand(1, $daysInMonth));
                $dueDate = $issueDate->copy()->addMonth();
                $paidAt = $dueDate->copy();

                $invoices[] = [
                    'customer_id' => rand(1, 300),
                    'package_id' => $packageId,
                    'invoice_number' => 'INV-2025' . str_pad($month, 2, '0', STR_PAD_LEFT) . str_pad($invoiceCount, 3, '0', STR_PAD_LEFT),
                    'issue_date' => $issueDate->format('Y-m-d'),
                    'due_date' => $dueDate->format('Y-m-d'),
                    'amount' => $amount,
                    'tax_amount' => $taxAmount,
                    'total_amount' => $totalAmount,
                    'status' => $paymemtStatus[array_rand($paymemtStatus)],
                    'paid_at' => $paidAt->format('Y-m-d H:i:s'),
                    'notes' => 'Pembayaran via ' . $paymentMethods[array_rand($paymentMethods)],
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                $invoiceCount++;
            }
        }

        // Insert invoices
        foreach ($invoices as $data) {
            try {
                $invoice = Invoice::create($data);

                // Update customer due_date
                // Customer::where('id', $data['customer_id'])->update([
                //     'due_date' => $data['due_date'],
                // ]);

                // Create transaction

                if ($data['status'] === 'paid') {
                     Transaction::create([
                    'invoice_id' => $invoice->id,
                    'customer_id' => $data['customer_id'],
                    'amount' => $data['amount'],
                    'payment_date' => $data['paid_at'],
                    'payment_method' => explode(' ', $data['notes'])[2] ?? 'Transfer',
                    'reference' => $data['invoice_number'],
                    'notes' => $data['notes'],
                ]);
                }


                $this->command->info("Invoice {$invoice->invoice_number} created for customer {$invoice->customer_id}");
            } catch (\Exception $e) {
                $this->command->error("Failed to create invoice: " . $e->getMessage());
            }
        }
    }
}
