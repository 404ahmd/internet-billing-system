<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\IpPool;
use App\Models\Package;
use App\Models\Report;
use App\Models\Router;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RouterOS\Client;
use RouterOS\Query;

class AdminController extends Controller
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

        return view('admin.dashboard', [
            'monthlyRevenue' => $monthlyRevenue,
            'paymentStatus' => $paymentStatus,
            'months' => $months,
            'transactionsCount' => $transactionsCount,
            //'transactionsAmount' => $transactionsAmount,
        ]);
    }

    // CUSTOMER SECTION ==================

    public function indexAdmin()
    {
        $customers = Customer::latest()->paginate(10);
        $invoices = Invoice::all();
        $packages = Package::all();
        return view('admin.customer_manage.customer_management', [
            'invoices' => $invoices,
            'customers' => $customers,
            'packages' => $packages
        ]);
    }

    public function store(Request $request)
    {
        // validation in form

        $validated = $request->validate([
            'name' => 'required',
            'username' => 'required|unique:customers,username',
            'phone' => 'required|digits_between:10,15',
            'address' => 'required',
            'package' => 'required',
            'group' => 'required',
            'join_date' => 'required',
            'status' => 'required',
            'notes' => 'required'
        ]);

        try {
            Customer::create($validated);
            return redirect()->route('admin.customer.view')->with('success', 'data berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'gagal menyimpan data' . $e->getMessage())->withInput();
        }
        //while success, redirect to form and throwing success message
    }

    public function destroy($id)
    {
        $customer_id = Customer::findOrFail($id);
        //check if customer id is empty or cant found
        //redirect back to table and show error message
        if (!$customer_id) {
            return redirect()->route('admin.customer.view')->with('error', 'data tidak bisa dihapus');
        }

        //if the customer id found in database
        //redirec with success message
        $customer_id->delete();
        return redirect()->route('admin.customer.view')->with('success', 'data berhasil dihapus');
    }

    public function edit(Customer $customer)
    {
        return view('admin.customer_manage.edit_customer', [
            'customer' => $customer,
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'string|max:255',
            'username' => 'string|max:255|unique:customers,username,' . $customer->id,
            'package' => 'string|max:255',
            'address' => 'string',
            'group' => 'nullable|string|max:255',
            'phone' => 'string|max:20',
            'join_date' => 'date',
            'status' => 'in:active,inactive,terminated',
            'last_payment_date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        try {
            $customer->update($validated);
            return redirect()->route('admin.customer.view')->with('success', 'Customer updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal merubah data pelanggan: ' . $e->getMessage())->withInput();
        }
    }


    public function search(Request $request)
    {
        $request->validate([
            'keyword' => 'string|max:255'
        ]);

        $keyword = $request->keyword;
        $customers = DB::table('customers')->where('name', 'like', '%' . $keyword . '%')->paginate();
        return view('admin.customer_manage.customer_result', compact('customers'));
    }

    // ACTIVATION CUSTOMER SECTION
    public function indexActivationCustomerAdmin()
    {
        $customers = Customer::all();
        $packages = Package::all();
        return view('admin.customer_activate.activation-customer', compact(['customers', 'packages']));
    }

    public function storeActivationAdmin(Request $request)
    {
        // Validasi request (tanpa invoice_number karena kita generate sendiri)
        $validated = $request->validate([
            'customer_id'   => 'required|exists:customers,id',
            'package_id'    => 'required|exists:packages,id',
            'issue_date'    => 'required|date',
            'due_date'      => 'required|date',
            'amount'        => 'required|numeric',
            'tax_amount'    => 'nullable|numeric',
            'total_amount'  => 'required|numeric',
            'status'        => 'required|in:paid,unpaid,overdue',
            'paid_at'       => 'nullable|date',
            'notes'         => 'nullable|string',
        ]);

        try {
            // Generate nomor invoice otomatis
            $date = Carbon::parse($validated['due_date'])->format('Ymd');
            $countToday = Invoice::whereDate('due_date', $validated['due_date'])->count() + 1;
            $invoiceNumber = 'INV-' . $date . '-' . str_pad($countToday, 3, '0', STR_PAD_LEFT);

            // Jika status paid tapi paid_at kosong, isi otomatis
            if ($validated['status'] === 'paid' && empty($validated['paid_at'])) {
                $validated['paid_at'] = now();
            }

            // Simpan invoice
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'customer_id'    => $validated['customer_id'],
                'package_id'     => $validated['package_id'],
                'issue_date'     => $validated['issue_date'],
                'due_date'       => $validated['due_date'],
                'amount'         => $validated['amount'],
                'tax_amount'     => $validated['tax_amount'] ?? 0,
                'total_amount'   => $validated['total_amount'],
                'status'         => $validated['status'],
                'paid_at'        => $validated['paid_at'],
                'notes'          => $validated['notes'],
            ]);

            // Update due_date ke customer
            Customer::where('id', $validated['customer_id'])->update([
                'due_date' => $validated['due_date'],
                'package' => $validated['package_id']
            ]);

            // Jika status paid, buat transaksi otomatis
            if ($validated['status'] === 'paid') {
                Transaction::firstOrCreate([
                    'invoice_id'     => $invoice->id,
                    'customer_id'    => $validated['customer_id'],
                    'amount'         => $validated['amount'],
                    'payment_date'   => $validated['paid_at'],
                    'payment_method' => 'Cash',
                    'reference'      => '',
                    'notes'          => 'Pembayaran invoice nomor ' . $invoiceNumber,
                ]);

                Customer::where('id', $validated['customer_id'])->update(['status' => 'active']);
            }

            return redirect()->back()->with('success', 'Invoice berhasil disimpan & pelanggan diaktivasi.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyimpan invoice: ' . $e->getMessage())
                ->withInput();
        }
    }

    // PACKAGE SECTION==============================
    public function indexPackageAdmin()
    {
        // variable for get all package
        // return the view package table
        $packages = Package::all();
        return view('admin.package_manage.package_management', compact('packages'));
    }

    // function for store new package to package table
    public function storePackageAdmin(Request $request)
    {
        // validation on input package form
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'type' => 'required',
            'cycle' => 'required',
            'bandwidth' => 'required',
            'status' => 'required'
        ]);

        //check if package is existing
        $existing = Package::where('description', $request->description)->first();
        if ($existing) {
            //retun back with error message
            return redirect()->back()->with('error', 'paket sudah tersedia');
        }

        // ctreate new package adn store to database
        Package::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'type' => $request->type,
            'cycle' => $request->cycle,
            'bandwidth' => $request->bandwidth,
            'status' => $request->status
        ]);

        // return back if success and show the message
        return redirect()->back()->with('success', 'paket berhasil ditambahkan');
    }

    public function editPacakgeAdmin($id)
    {
        $packages = Package::findOrFail($id);
        return view('admin.package_manage.package_edit', [
            'packages' => $packages,
        ]);
    }

    public function updatePackageAdmin(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'type' => 'required',
            'cycle' => 'required',
            'bandwidth' => 'required',
            'status' => 'required'
        ]);

        try {
            $pacakge = Package::findOrFail($id);
            $pacakge->update($validated);
            return redirect()->route('admin.package.view')->with('success', 'paket berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'gagal memperbarui paket' . $e->getMessage())->withInput();
        }
    }

    public function destroyPackageAdmin($id)
    {
        $id_package = Package::findOrFail($id);

        if (!$id_package) {
            return redirect()->back()->with('error', 'data gagal dihapus');
        }

        $id_package->delete();
        return redirect()->route('admin.package.view')->with('success', 'data berhasil dihapus');
    }

    // ACTIVITY SECTION ===================
    public function adminActivityReport()
    {
        $reports = Report::select('source', 'description', 'created_at')
            ->latest()
            ->paginate(10);
        return view('admin.activity.activity_report', [
            'reports' => $reports
        ]);
    }

    // INVOICE SECTION
    public function indexInvoiceAdmin()
    {
        $invoices = Invoice::latest()->paginate(10);
        return view('admin.invoice.invoice_customer', [
            'invoices' => $invoices,
        ]);
    }

    public function searchInvoiceAdmin(Request $request)
    {
        $keyword = $request->input('customer_name');

        $invoices = Invoice::with('customer')
            ->whereHas('customer', function ($query) use ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            })
            ->paginate(10);

        return view('admin.invoice.invoice_customer', compact('invoices', 'keyword'));
    }

    //get form invoice update
    public function editInvoiceAdmin(Invoice $invoice)
    {
        $customers = Customer::all();
        $packages = Package::all();
        return view('admin.invoice.update_invoice', [
            'invoice' => $invoice,
            'packages' => $packages,
            'customers' => $customers
        ]);
    }

    public function updateInvoiceAdmin(Request $request, Invoice $invoice)
    {

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'package_id' => 'required|exists:packages,id',
            'invoice_number' => 'required|unique:invoices,invoice_number,' . $invoice->id,
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'amount' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:paid,unpaid,overdue',
            'paid_at' => 'nullable|date', //$request->status == 'paid' ? 'required|date' :
            'notes' => 'nullable|string',
        ]);

        if ($validated['status'] === 'paid' && empty($validated['paid_at'])) {
            $validated['paid_at'] = now();
        } elseif ($validated['status'] !== 'paid') {
            $validated['paid_at'] = null;
        }

        try {
            $invoice->update($validated);
            return redirect()->route('admin.invoice.view')->with('success', 'data berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'gagal mengupdate data' . $e->getMessage())->withInput();
        }
    }

    public function destroyInvoiceAdmin(Invoice $invoice)
    {
        try {
            $invoice->delete();
            return redirect()->route('admin.invoice.view')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal Menghapus Data' . $e->getMessage());
        }
    }

    // TRANSACTIONS SECTION
    public function indexTransactionsAdmin()
    {
        $transactions = Transaction::latest()->paginate(10);
        return view('admin.transactions.transactions_customer', [
            'transactions' => $transactions
        ]);
    }

    public function searchTransactionsAdmin(Request $request)
    {
        $request->validate([
            'keyword' => 'string|max:255'
        ]);

        $keyword = $request->keyword;
        //$transactions = DB::table('transactions')->where('customer_name', 'like', '%'. $keyword .'%')->paginate();

        $transactions = Transaction::whereHas('customer', function ($query) use ($keyword) {
            $query->where('name', 'like', '%' . $keyword . '%');
        })->paginate(10);
        return view('admin.transactions.transactions_customer', compact('transactions'));
    }

    //CUSTOMER ARREARS
    public function adminCustomerArrears()
    {
        $customers = Customer::whereHas('invoices', function ($query) {
            $query->where('status', 'unpaid');
        })
            ->with(['invoices' => function ($query) {
                $query->where('status', 'unpaid')
                    ->orderBy('due_date', 'asc');
            }])->paginate(10);

        return view('admin.customer_manage.customer_arrears', [
            'customers' => $customers,
        ]);
    }

    // CUSTOMER DATA
    public function adminGetCustomerData()
    {
        $customers = Customer::select('name', 'username', 'package', 'group', 'status')->latest()
            ->paginate(10);

        return view('admin.customer_manage.customer_data', [
            'customers' => $customers,
        ]);
    }

    // ROUTER SECTION ======================================
    //ROUTER SECTION ================================================
    public function indexAdminRouter()
    {
        $routers = Router::all();
        return view('admin.router.router_dashboard', compact('routers'));
    }

    public function connectAdminRouter(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'host' => 'required|ip',
            'username' => 'required|string',
            'password' => 'required|string',
            'port' => 'nullable|integer|min:1|max:65535'
        ]);

        try {
            $client = new Client([
                'host' => trim($validated['host']),
                'user' => trim($validated['username']),
                'pass' => trim($validated['password']),
                'port' => $validated['port'] ?? 8728,
                'timeout' => 3,
                'attempts' => 1
            ]);

            // Test koneksi dengan query sederhana
            $client->query(new Query('/system/resource/print'))->read();

            // Simpan router ke database
            $router = Router::create([
                'name' => $validated['name'],
                'host' => $validated['host'],
                'username' => $validated['username'],
                'password' => encrypt($validated['password']), // Enkripsi password
                'port' => $validated['port'] ?? 8728,
                'is_active' => true,
                'last_seen_at' => now()
            ]);

            return redirect()->route('admin.router.view')
                ->with('success', 'Router berhasil ditambahkan dan terhubung');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['connection_error' => 'Gagal terhubung ke router: ' . $e->getMessage()]);
        }
    }

    public function getAdminRouterStatus($routerId)
    {
        $router = Router::findOrFail($routerId);

        try {
            // Decrypt password sebelum digunakan
            $decryptedPassword = decrypt($router->password);

            $client = new Client([
                'host' => $router->host,
                'user' => $router->username,
                'pass' => $decryptedPassword,
                'port' => $router->port ?? 8728,
                'timeout' => 3,
                'attempts' => 1
            ]);

            // Ambil data dari router
            $resource = $client->query(new Query('/system/resource/print'))->read();
            $identity = $client->query(new Query('/system/identity/print'))->read();
            $interfaces = $client->query(new Query('/interface/print'))->read();

            // Update status router
            $router->update([
                'is_active' => true,
                'last_seen_at' => now(),
            ]);

            return response()->json([
                'online' => true,
                'data' => [
                    'identity' => $identity[0]['name'] ?? $router->name,
                    'cpu_load' => $resource[0]['cpu-load'] ?? 'N/A',
                    'uptime' => $this->formatUptime($resource[0]['uptime'] ?? 0),
                    'memory_usage' => round(($resource[0]['free-memory'] / $resource[0]['total-memory']) * 100, 2),
                    'interface_count' => count($interfaces),
                    'version' => $resource[0]['version'] ?? 'N/A'
                ]
            ]);
        } catch (\Exception $e) {
            $router->update(['is_active' => false]);

            return response()->json([
                'online' => false,
                'error' => $e->getMessage(),
                'last_seen' => $router->last_seen_at ? $router->last_seen_at->format('Y-m-d H:i:s') : 'Never'
            ]);
        }
    }

    public function destroyRouter(Router $router)
    {
        try {
            if ($router->IpPolls()->count() > 0) {
                return redirect()->back()->with('error', 'Router masih digunakan oleh Ip Pool, silahkan hapus Ip pool terlebih dahulu');
            }

            $router->delete();
            return redirect()->route('admin.router.view')->with('success', 'Router berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors([
                'error' => 'Gagal menghapus router: ' . $e->getMessage()
            ]);
        }
    }

    private function formatUptime($seconds)
    {
        $seconds = (int)$seconds;
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        return sprintf('%d hari %d jam %d menit', $days, $hours, $minutes);
    }

     // IP POOL SECTION
    public function createIpPool()
    {
        $routers = Router::all();
        $ip_pools = IpPool::latest()->paginate(10);
        return view('admin.ip_pool.create', [
            'routers' => $routers,
            'ip_pools' => $ip_pools
        ]);
    }

    public function storeIpPool(Request $request)
    {
        // Validasi input
        $data = $request->validate([
            'name' => 'required',
            'range' => 'required',
            'router_id' => 'required|exists:routers,id',
        ]);

        if (!isset($data['router_id'])) {
            return back()
                ->withInput()
                ->withErrors(['router_id' => 'Router ID harus dipilih']);
        }

        // Dapatkan router
        $router = Router::findOrFail($request->router_id);

        try {

            $password = $this->decryptPassword($router->password);
            // Buat koneksi ke MikroTik
            $client = new Client([
                'host' => $router->host,
                'user' => $router->username,
                'pass' => $password,
                'timeout' => 10, // tambahkan timeout
                'attempts' => 2, // jumlah percobaan
            ]);

            // Periksa apakah pool dengan nama yang sama sudah ada di router
            $checkQuery = new Query('/ip/pool/print');
            $checkQuery->where('name', $data['name']);
            $existingPools = $client->query($checkQuery)->read();

            if (!empty($existingPools)) {
                return back()
                    ->withInput()
                    ->withErrors(['name' => 'IP Pool dengan nama ini sudah ada di router']);
            }

            $range = preg_replace('/\s+/', '', $data['range']); // Hilangkan semua spasi

            // Tambahkan pool ke MikroTik
            $client->query((new Query('/ip/pool/add'))
                ->equal('name', trim($data['name']))
                ->equal('ranges', $data['range']))
                ->read();

            IpPool::create([
                'router_id' => $router->id,
                'name' => $data['name'],
                'range' => $data['range'],
            ]);

            return redirect()->back() // lebih baik redirect ke route tertentu
                ->with('success', 'IP Pool berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['connection_error' => 'Gagal menambahkan IP Pool: ' . $e->getMessage()]);
        }
    }

    public function destroyIpPool($id)
    {

        $ipPool = IpPool::findOrFail($id);

        try {
            $router = $ipPool->router;

            if (!$router) {
                return back()->with('error', 'router tidak ditemukan');
            }

            $password = $this->decryptPassword($router->password);
            $client = new Client([
                'host' => $router->host,
                'user' => $router->username,
                'pass' => $password,
                'port' => $router->port ?? 8728,
                'timeout' => 10,
                'attempts' => 2,
            ]);

            $checkQuery = new Query('/ip/pool/print');
            

            $checkQuery->where('name', $ipPool->name);
            $existingPools = $client->query($checkQuery)->read();


            if (!empty($existingPools)) {
                $poolId = $existingPools[0]['.id'];

                //hapus ip pool
                $removeQuery = new Query('/ip/pool/remove');
                $removeQuery->equal('.id', $poolId);
                $client->query($removeQuery)->read();
            }

            $ipPool->delete();

            return redirect()->route('admin.ip-pool.create')->with('success', 'Ip Pool berhasil dihapus');
        } catch (\Exception $e) {
            return back()->withErrors(["connection_error" => "gagal menghapus ip pool" . $e->getMessage()]);
        }
    }
}
