<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomersExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Customer::select([
            'id',
            'name',
            'username',
            'package',
            'address',
            'group',
            'phone',
            'join_date',
            'status',
            'last_payment_date',
            'due_date',
            'notes',
            'created_at',
            'updated_at'
        ])->get()->map(function ($item) {
            // Convert date strings to Carbon instances
            $item->join_date = $item->join_date ? \Carbon\Carbon::parse($item->join_date) : null;
            $item->last_payment_date = $item->last_payment_date ? \Carbon\Carbon::parse($item->last_payment_date) : null;
            $item->due_date = $item->due_date ? \Carbon\Carbon::parse($item->due_date) : null;
            return $item;
        });
    }

    /**
     * Map data for each row
     */
    public function map($customer): array
    {
        return [
            $customer->id,
            $customer->name,
            $customer->username,
            $customer->package,
            $customer->address,
            $customer->group,
            $customer->phone,
            $customer->join_date ? $customer->join_date->format('Y-m-d') : '',
            ucfirst($customer->status),
            $customer->last_payment_date ? $customer->last_payment_date->format('Y-m-d') : '',
            $customer->due_date ? $customer->due_date->format('Y-m-d') : '',
            $customer->notes,
            $customer->created_at ? $customer->created_at->format('Y-m-d H:i:s') : '',
            $customer->updated_at ? $customer->updated_at->format('Y-m-d H:i:s') : '',
        ];
    }

    /**
     * Column headings
     */
    public function headings(): array
    {
        return [
            'ID',
            'Nama Lengkap',
            'Username',
            'Paket Layanan',
            'Alamat',
            'Group',
            'Nomor Telepon',
            'Tanggal Bergabung',
            'Status',
            'Tanggal Pembayaran Terakhir',
            'Tanggal Jatuh Tempo',
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
        return 'Data Pelanggan';
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

            // Add borders to all cells
            'A1:N' . ($sheet->getHighestRow()) => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ]
                ]
            ]
        ];
    }
}
