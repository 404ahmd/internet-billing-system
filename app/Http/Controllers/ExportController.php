<?php

namespace App\Http\Controllers;

use App\Exports\CustomersExport;
use App\Exports\InvoiceExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportCustomerData(){
        return Excel::download(new CustomersExport, 'customers.xlsx');
    }

    public function exportInvoiceData(){
        return Excel::download(new InvoiceExport, 'invoices.xlsx');
    }
}
