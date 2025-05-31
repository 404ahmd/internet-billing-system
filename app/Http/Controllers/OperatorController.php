<?php

namespace App\Http\Controllers;

use App\Events\RouterStatusUpdated;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\IpPool;
use App\Models\Package;
use App\Models\PppoeService;
use App\Models\PppProfiles;
use App\Models\PppSecret;
use App\Models\Report;
use App\Models\Router;
use App\Models\RouterStat;
use App\Models\Transaction;
use App\Services\RouterOsService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use RouterOS\Client;
use RouterOS\Query;
use Illuminate\Support\Str;

class OperatorController extends Controller
{
    public function dashboard()
    {
        return view('operator.dashboard');
    }

    // CUSTOMER SECTION ===================================================================================

    public function indexOperator()
    {
        $customers = Customer::latest()->paginate(10);
        $invoices = Invoice::all();
        $packages = Package::all();
        return view('operator.customer.customer_management', [
            'invoices' => $invoices,
            'customers' => $customers,
            'packages' => $packages
        ]);
    }

    public function storeCustomerOperator(Request $request)
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
            return redirect()->route('operator.customer.view')->with('success', 'data berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'gagal menyimpan data' . $e->getMessage())->withInput();
        }
        //while success, redirect to form and throwing success message
    }

    public function destroyCustomerOperator($id)
    {
        $customer_id = Customer::findOrFail($id);
        //check if customer id is empty or cant found
        //redirect back to table and show error message
        if (!$customer_id) {
            return redirect()->route('operator.customer.view')->with('error', 'data tidak bisa dihapus');
        }

        //if the customer id found in database
        //redirec with success message
        $customer_id->delete();
        return redirect()->route('operator.customer.view')->with('success', 'data berhasil dihapus');
    }

    public function editCustomerOperator(Customer $customer)
    {
        return view('operator.customer.edit_customer', [
            'customer' => $customer,
        ]);
    }

    public function updateCustomerOperator(Request $request, Customer $customer)
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
            return redirect()->route('operator.customer.view')->with('success', 'Customer updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal merubah data pelanggan: ' . $e->getMessage())->withInput();
        }
    }

    public function searchCustomerOperator(Request $request)
    {
        $request->validate([
            'keyword' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive,terminated'
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

        return view('operator.customer.customer_management', [
            'customers' => $customers,
            'packages' => $packages,
            'invoices' => $invoices,
            'keyword' => $keyword,
            'status' => $status,
        ]);
    }



    //ACTIVATION CUSTOMER SECTION ===========================================================================
    public function indexActivationCustomerOperator()
    {
        $customers = Customer::all();
        $packages = Package::all();
        return view('operator.customer.activation-customer', compact(['customers', 'packages']));
    }

    public function storeActivationOperator(Request $request)
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

    // PACKAGE SECTION ================================================================================
    public function indexPackageOperator()
    {
        // variable for get all package
        // return the view package table
        $packages = Package::all();
        return view('operator.package.package_management', compact('packages'));
    }

    // function for store new package to package table
    public function storePackageOperator(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required',
            'type' => 'required',
            'cycle' => 'required',
            'bandwidth' => 'required',
            'status' => 'required'
        ]);

        try {
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
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Tidak bisa menambahkan paket');
        }
    }

    public function editPacakgeOperator($id)
    {
        $packages = Package::findOrFail($id);
        return view('operator.package.package_edit', [
            'packages' => $packages,
        ]);
    }

    public function updatePackageOperator(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required',
            'price' => 'required',
            'type' => 'required',
            'cycle' => 'required',
            'bandwidth' => 'required',
            'status' => 'required'
        ]);

        try {
            $pacakge = Package::findOrFail($id);
            $pacakge->update($validated);
            return redirect()->route('operator.package.view')->with('success', 'paket berhasil diperbarui');
        } catch (\Exception $e) {
            return back()->with('error', 'gagal memperbarui paket' . $e->getMessage())->withInput();
        }
    }

    public function destroyPackageOperator($id)
    {
        $id_package = Package::findOrFail($id);

        if (!$id_package) {
            return redirect()->back()->with('error', 'data gagal dihapus');
        }

        $id_package->delete();
        return redirect()->route('admin.package.view')->with('success', 'data berhasil dihapus');
    }

    // INVOICE SECTION ================================================================================
    public function indexInvoiceOperator()
    {
        $invoices = Invoice::latest()->paginate(10);
        return view('operator.invoice.invoice_customer', [
            'invoices' => $invoices,
        ]);
    }

    public function searchInvoiceOperator(Request $request)
    {
        $keyword = $request->input('customer_name');

        $invoices = Invoice::with('customer')
            ->whereHas('customer', function ($query) use ($keyword) {
                $query->where('name', 'like', '%' . $keyword . '%');
            })->latest()
            ->paginate(10);

        return view('operator.invoice.invoice_customer', compact('invoices', 'keyword'));
    }

    //get form invoice update
    public function editInvoiceOperator(Invoice $invoice)
    {
        $customers = Customer::all();
        $packages = Package::all();
        return view('operator.invoice.update_invoice', [
            'invoice' => $invoice,
            'packages' => $packages,
            'customers' => $customers
        ]);
    }

    public function updateInvoiceOperator(Request $request, Invoice $invoice)
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
            return redirect()->route('operator.invoice.view')->with('success', 'data berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'gagal mengupdate data' . $e->getMessage())->withInput();
        }
    }

    public function destroyInvoiceOperator(Invoice $invoice)
    {
        try {
            $invoice->delete();
            return redirect()->route('operator.invoices.view')->with('success', 'Data Berhasil Dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal Menghapus Data' . $e->getMessage());
        }
    }

    // TRANSACTIONS SECTION ============================================================================
    public function indexTransactionsOperator()
    {
        $transactions = Transaction::latest()->paginate(10);
        return view('operator.transactions.transactions_customer', [
            'transactions' => $transactions
        ]);
    }

    public function searchTransactionsOperator(Request $request)
    {
        $request->validate([
            'keyword' => 'string|max:255'
        ]);

        $keyword = $request->keyword;
        //$transactions = DB::table('transactions')->where('customer_name', 'like', '%'. $keyword .'%')->paginate();

        $transactions = Transaction::whereHas('customer', function ($query) use ($keyword) {
            $query->where('name', 'like', '%' . $keyword . '%');
        })->paginate(10);
        return view('operator.transactions.transactions_customer', compact('transactions'));
    }

    //ROUTER SECTION ===================================================================================
    public function indexOperatorRouter()
    {
        $routers = Router::all();
        return view('operator.router.router_dashboard', compact('routers'));
    }

    public function connectOperatorRouter(Request $request)
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

            return redirect()->route('operator.router.view')
                ->with('success', 'Router berhasil ditambahkan dan terhubung');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['connection_error' => 'Gagal terhubung ke router: ' . $e->getMessage()]);
        }
    }

    public function getOperatorRouterStatus($routerId)
    {
        $router = Router::findOrFail($routerId);

        try {
            // Decrypt password sebelum digunakan
            $password = $this->decryptPassword($router->password);

            $client = new Client([
                'host' => $router->host,
                'user' => $router->username,
                'pass' => $password,
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
            // if ($router->IpPolls()->count() > 0) {
            //     return redirect()->back()->with('error', 'Router masih digunakan oleh Ip Pool, silahkan hapus Ip pool terlebih dahulu');
            // }

            $router->delete();
            return redirect()->route('operator.router.view')->with('success', 'Router berhasil dihapus');
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

    // DECRYPT PASSWORD

    private function decryptPassword($encryptedPassword)
    {
        try {
            // Jika password dienkripsi dengan Laravel encrypt()
            if (Str::startsWith($encryptedPassword, 'eyJpdiI6')) {
                return Crypt::decrypt($encryptedPassword);
            }

            // Jika password tidak dienkripsi (plain text)
            return $encryptedPassword;
        } catch (\Exception $e) {
            throw new \Exception("Failed to decrypt router password: " . $e->getMessage());
        }
    }

    // IP POOL SECTION ======================================================================================
    public function createIpPool()
    {
        $routers = Router::all();
        $ip_pools = IpPool::latest()->paginate(10);
        return view('operator.ip_pool.create', [
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

            return redirect()->route('operator.ip-pool.create')->with('success', 'Ip Pool berhasil dihapus');
        } catch (\Exception $e) {
            return back()->withErrors(["connection_error" => "gagal menghapus ip pool" . $e->getMessage()]);
        }
    }

    // PPP PROFILE SECTION ========================================================================
    public function createPppProfile()
    {
        $routers = Router::all();
        $profiles = PppProfiles::with('router')->get();
        return view('operator.pppoe.ppp-profile', [
            'routers' => $routers,
            'profiles' => $profiles,
        ]);
    }

    public function storePppProfile(Request $request)
    {
        $validated = $request->validate([
            'router_id' => 'required|exists:routers,id',
            'name' => 'required|string',
            'local_address' => 'nullable|string',
            'remote_address' => 'nullable|string',
            'rate_limit' => 'nullable|string',
        ]);

        $router = Router::findOrFail($validated['router_id']);
        $password = $this->decryptPassword($router->password);

        try {
            $client = new Client([
                'host' => $router->host,
                'user' => $router->username,
                'pass' => $password,
                'port' => $router->port ?? 8728,
            ]);

            $query = new Query('/ppp/profile/add');
            $query->equal('name', $validated['name']);
            if (!empty($validated['local_address'])) {
                $query->equal('local-address', $validated['local_address']);
            }

            if (!empty($validated['remote_address'])) {
                $query->equal('remote-address', $validated['remote_address']);
            }
            if ($validated['rate_limit']) {
                $query->equal('rate-limit', $validated['rate_limit']);
            }

            $client->query($query)->read();

            PppProfiles::create($validated);

            return redirect()->route('operator.ppp-profile.create')->with('success', 'Profile Berhasil Ditambahkan');
        } catch (\Exception $e) {
            return back()->withErrors(["error", "Gagal membuat profile" . $e->getMessage()]);
        }
    }

    public function removePppProfile($id)
    {
        $profile = PppProfiles::findOrFail($id);
        try {
            $router = $profile->router;
            $password = $this->decryptPassword($router->password);

            $client = new Client([
                'host' => $router->host,
                'user' => $router->username,
                'pass' => $password,
                'port' => $router->port ?? 8728
            ]);

            $query = new Query('/ppp/profile/print');
            $query->where('name', $profile->name);

            $result = $client->query($query)->read();

            if (!empty($result)) {
                $profileId = $result[0]['.id']; // Dapatkan internal MikroTik ID
                $deleteQuery = new Query('/ppp/profile/remove');
                $deleteQuery->equal('.id', $profileId);

                $client->query($deleteQuery)->read();
            }

            $profile->delete();

            return redirect()->route('operator.ppp-profile.create')->with("success", 'profile ppp brhasil dihapus');
        } catch (\Exception $e) {
            return back()->withErrors(['error', 'ppp profile tidak dapat dihapus' . $e->getMessage()]);
        }
    }


    // PPP SECRET SECTION =======================================================================

    public function createPppSecretes()
    {
        $routers = Router::all();
        $profiles = PppProfiles::all();
        $secrets = PppSecret::all();
        return view('operator.pppoe.ppp-secret', [
            'routers' => $routers,
            'profiles' => $profiles,
            'secrets' => $secrets
        ]);
    }

    public function storePppSecrets(Request $request)
    {
        $validated = $request->validate([
            'router_id' => 'required|exists:routers,id',
            'name' => 'required|string',
            'password' => 'required|string',
            'service' => 'required|string',
            'profile' => 'required',
            'local_address' => 'nullable|string',
            'remote_address' => 'nullable|string',
            'comment' => 'nullable'
        ]);

        $router = Router::findOrFail($validated['router_id']);
        $password = $this->decryptPassword($router->password);

        try {
            $client = new Client([
                'host' => $router->host,
                'user' => $router->username,
                'pass' => $password,
                'port' => $router->port ?? 8728,
            ]);

            $query = new Query('/ppp/secret/add');
            $query->equal('name', $validated['name'])
                ->equal('password', $validated['password'])
                ->equal('service', $validated['service'])
                ->equal('profile', $validated['profile'])
                ->equal('comment', $validated['comment']);


            if (!empty($validated['local_address'])) {
                $query->equal('local-address', $validated['local_address']);
            }

            if (!empty($validated['remote_address'])) {
                $query->equal('remote-address', $validated['remote_address']);
            }

            $client->query($query)->read();

            PppSecret::create($validated);

            return redirect()->route('operator.ppp-secret.create')->with('success', 'secret berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(["error", "gagal menambah secret baru" . $e->getMessage()]);
        }
    }

    public function removePppSecrets($id)
    {
        $secrets = PppSecret::findOrFail($id);

        try {
            $router = $secrets->router;
            $password = $this->decryptPassword($router->password);
            $client = new Client([
                'host' => $router->host,
                'user' => $router->username,
                'pass' => $password,
                'port' => $router->port ?? 8728
            ]);

            $query = new Query('/ppp/secret/print');
            $query->where('name', $secrets->name);

            $result = $client->query($query)->read();

            if (!empty($result)) {
                $secretId = $result[0]['.id']; // Dapatkan internal MikroTik ID
                $deleteQuery = new Query('/ppp/secret/remove');
                $deleteQuery->equal('.id', $secretId);


                $client->query($deleteQuery)->read();
            }

            $secrets->delete();
            return redirect()->route('operator.ppp-secret.create')->with('success', 'secret berhasil dihapus');
        } catch (\Exception $e) {
            return back()->withErrors(["error", "secret tidak bisa dihapus " . $e->getMessage()]);
        }
    }
}
