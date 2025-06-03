<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoiceExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Invoice::with(['customer:id,name', 'package:id,name'])
            ->select([
                'id',
                'customer_id',
                'package_id',
                'invoice_number',
                'issue_date',
                'due_date',
                'amount',
                'tax_amount',
                'total_amount',
                'status',
                'paid_at',
                'notes',
                'created_at',
                'updated_at'
            ])
            ->get()
            ->map(function ($invoice) {
                // Convert dates to Carbon instances
                $invoice->issue_date = $invoice->issue_date ? \Carbon\Carbon::parse($invoice->issue_date) : null;
                $invoice->due_date = $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date) : null;
                $invoice->paid_at = $invoice->paid_at ? \Carbon\Carbon::parse($invoice->paid_at) : null;
                return $invoice;
            });
    }

    /**
     * Map data for each row
     */
    public function map($invoice): array
    {
        return [
            $invoice->id,
            $invoice->customer->name ?? 'N/A', // Nama customer
            $invoice->package->name ?? 'N/A',  // Nama package
            $invoice->invoice_number,
            $invoice->issue_date ? $invoice->issue_date->format('Y-m-d') : '',
            $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '',
            number_format($invoice->amount, 2),
            number_format($invoice->tax_amount, 2),
            number_format($invoice->total_amount, 2),
            ucfirst($invoice->status),
            $invoice->paid_at ? $invoice->paid_at->format('Y-m-d H:i:s') : '',
            $invoice->notes,
            $invoice->created_at ? $invoice->created_at->format('Y-m-d H:i:s') : '',
            $invoice->updated_at ? $invoice->updated_at->format('Y-m-d H:i:s') : '',
        ];
    }

    /**
     * Column headings
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nama Customer',
            'Nama Paket',
            'Nomor Invoice',
            'Tanggal Invoice',
            'Jatuh Tempo',
            'Jumlah',
            'Pajak',
            'Total',
            'Status',
            'Tanggal Pembayaran',
            'Catatan',
            'Dibuat Pada',
            'Diupdate Pada'
        ];
    }

    /**
     * Worksheet title
     */
    public function title(): string
    {
        return 'Data Invoice';
    }

    /**
     * Style the sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],

            // Style the header row
            'A1:N1' => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFD9D9D9']
                ]
            ],

            // Set auto size for all columns
            'A:N' => [
                'autoSize' => true
            ],

            // Format currency columns
            'G2:I'.$sheet->getHighestRow() => [
                'numberFormat' => [
                    'formatCode' => '#,##0.00'
                ]
            ],

            // Add borders to all cells
            'A1:N'.$sheet->getHighestRow() => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ]
                ]
            ]
        ];
    }
}
