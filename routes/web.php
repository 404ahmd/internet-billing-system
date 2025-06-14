<?php

use App\Exports\CustomersExport;
use App\Http\Controllers\ActivationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\PppoeController;
use App\Http\Controllers\RouterOsController;
use App\Http\Controllers\TransactionController;
use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\Exponential;
use PHPUnit\Framework\Constraint\Operator;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use SebastianBergmann\Exporter\Exporter;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [AuthController::class, 'index']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::get('/member/login', [AuthController::class, 'loginMemberForm'])->name('member.login');
Route::post('/member/auth', [AuthController::class, 'loginMember'])->name('member.auth');
Route::get('/member/dashboard', [MemberController::class, 'dashboard'])->name('member.dashboard');

Route::middleware(['auth'])->group(function () {

    Route::get('/dump', function () {
        return view('operator.dum_content');
    })->name('dump');


    // FOR TESTING A ROUTER
    Route::get('/test/router', [RouterOsController::class, 'test']);


    Route::get('/user/manager', [DashboardController::class, 'manager'])->name('user.manager');
    Route::get('/dashboard/admin', [AdminController::class, 'dashboard'])->name('dashboard.admin');


    //AUTOCOMPLETE ======================
    Route::get('/autocomplete/customer', [CustomerController::class, 'autocomplete'])->name('customer.autocomplete');

    Route::get('/autocomplete/invoice', [InvoiceController::class, 'autocomplete'])->name('invoice.autocomplete');

    //FINANACE===========================
    Route::get('/finance/dashboard', [FinanceController::class, 'dashboard'])->name('finance.dashboard');
    Route::get('/finance/list/customer', [FinanceController::class, 'getAllInvoices'])->name('finance.list.customer');
    Route::get('/finance/transactions/history', [FinanceController::class, 'transactionsHistory'])->name('finance.transactions.history');
    Route::get('/finance/data/customers', [FinanceController::class, 'getCustomerData'])->name('finance.data.customers');
    Route::get('/finance/report', [FinanceController::class, 'activityReport'])->name('finance.report');
    Route::get('/finance/customer/arrears', [FinanceController::class, 'customerArrears'])->name('finance.customer.arrears');
    Route::get('/finance/customer/search', [FinanceController::class, 'search'])->name('finance.customer.search');
    Route::get('/finance/invoice/search', [FinanceController::class, 'searchInvoice'])->name('finance.invoices.search');
    Route::patch('/finance/{invoice}/markAsPaid', [FinanceController::class, 'markAsPaid'])->name('finance.invoice.markAsPaid');
    Route::get('/finance/invoices/unpaid/search', [FinanceController::class, 'searchUnpaid'])->name('finance.invoices.searchUnpaid');


    //ADMININSTRATOR======================
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/customer/view', [AdminController::class, 'indexAdmin'])->name('admin.customer.view');
    Route::post('/admin/customer/store', [AdminController::class, 'store'])->name('admin.customer.store');
    Route::get('/admin/customer/search', [AdminController::class, 'search'])->name('admin.customer.search');
    Route::delete('/admin/customer/{id}/destroy', [AdminController::class, 'destroy'])->name('admin.customer.destroy');
    Route::get('/admin/customer/{customer}/edit', [AdminController::class, 'edit'])->name('admin.customer.edit');
    Route::put('/admin/customer/{customer}/update', [AdminController::class, 'update'])->name('admin.customer.update');
    Route::get('/admin/autocomplete/customer', [AdminController::class, 'autocomplete'])->name('admin.customer.autocomplete');

    Route::get('/admin/customer/activation', [AdminController::class, 'indexActivationCustomerAdmin'])->name('admin.customer.activation');
    Route::post('/admin/customer/activation/store', [AdminController::class, 'storeActivationAdmin'])->name('admin.customer.activation.store');

    Route::get('/admin/pacakge/view', [AdminController::class, 'indexPackageAdmin'])->name('admin.package.view');
    Route::post('/admin/package/store', [AdminController::class, 'storePackageAdmin'])->name('admin.package.store');
    Route::get('/admin/package/{id}/edit', [AdminController::class, 'editPacakgeAdmin'])->name('admin.package.edit');
    Route::put('/admin/package/{id}/update', [AdminController::class, 'updatePackageAdmin'])->name('admin.package.update');
    Route::delete('/admin/package/{id}/destroy', [AdminController::class, 'destroyPackageAdmin'])->name('admin.package.destroy');

    Route::get('/admin/report', [AdminController::class, 'adminActivityReport'])->name('admin.report');

    Route::get('admin/invoice/view', [AdminController::class, 'indexInvoiceAdmin'])->name('admin.invoice.view');
    Route::get('admin/invoices/{invoice}/edit', [AdminController::class, 'editInvoiceAdmin'])->name('admin.invoices.edit');
    Route::put('admin/invoices/{invoice}', [AdminController::class, 'updateInvoiceAdmin'])->name('admin.invoices.update');
    Route::get('admin/invoices/search', [AdminController::class, 'searchInvoiceAdmin'])->name('admin.invoices.search');
    Route::delete('admin/invoice/{invoice}', [AdminController::class, 'destroyInvoiceAdmin'])->name('admin.invoice.delete');
    Route::patch('/admin/{invoice}/markAsPaid', [AdminController::class, 'markAsPaid'])->name('admin.invoice.markAsPaid');
    Route::get('/admin/invoices/unpaid/search', [AdminController::class, 'searchUnpaid'])->name('admin.invoices.searchUnpaid');


    Route::get('admin/transaction/search', [AdminController::class, 'searchTransactionsAdmin'])->name('admin.transaction.search');
    Route::get('admin/transaction/view', [AdminController::class, 'indexTransactionsAdmin'])->name('admin.transaction.view');

    Route::get('/admin/customer/arrears', [AdminController::class, 'adminCustomerArrears'])->name('admin.customer.arrears');

    Route::get('/admin/data/customers', [AdminController::class, 'adminGetCustomerData'])->name('admin.data.customers');

    Route::get('/admin/router/view', [AdminController::class, 'indexAdminRouter'])->name('admin.router.view');
    Route::post('/admin/router/connect', [AdminController::class, 'connectAdminRouter'])->name('admin.router.connect');
    Route::get('/admin/router/{id}/status', [AdminController::class, 'getAdminRouterStatus'])
        ->name('admin.router.status');
    Route::delete('/admin/router/{router}/destroy', [AdminController::class, 'destroyRouter'])->name('admin.destroy.router');

    Route::get('/admin/ip-pool/create', [AdminController::class, 'createIpPool'])->name('admin.ip-pool.create');
    Route::post('/admin/ip-pool/store', [AdminController::class, 'storeIpPool'])->name('admin.ip-pool.store');
    Route::delete('/admin/ip-pool/{id}/destroy', [AdminController::class, 'destroyIpPool'])->name('admin.ip-pool.destroy');

    Route::get('/admin/ppp-secret/create', [AdminController::class, 'createPppSecretes'])->name('admin.ppp-secret.create');
    Route::post('/admin/ppp-secret/store', [AdminController::class, 'storePppSecrets'])->name('admin.ppp-secret.store');
    Route::delete('/admin/ppp-secret/{id}/remove', [AdminController::class, 'removePppSecrets'])->name('admin.ppp-secret.remove');
        Route::get('/admin/ppp-secret/search', [AdminController::class, 'searchPppSecret'])->name('admin.ppp-secret.search');


    Route::get('/admin/ppp-profile/create', [AdminController::class, 'createPppProfile'])->name('admin.ppp-profile.create');
    Route::post('/admin/ppp-profile/post', [AdminController::class, 'storePppProfile'])->name('admin.ppp-profile.store');
    Route::delete('/admin/ppp-profile/{id}/remove', [AdminController::class, 'removePppProfile'])->name('admin.ppp-profile.remove');

    // OPERATOR =====================
    Route::get('/operator/dashboard', [OperatorController::class, 'dashboard'])->name('operator.dashboard');
    Route::get('/operator/mikrotik/stats', [OperatorController::class, 'getMikrotikStats']);
    Route::get('/operator/customer/view', [OperatorController::class, 'indexOperator'])->name('operator.customer.view');
    Route::post('/operator/customer/store', [OperatorController::class, 'storeCustomerOperator'])->name('operator.customer.store');
    Route::get('/operator/customer/search', [OperatorController::class, 'searchCustomerOperator'])->name('operator.customer.search');
    Route::delete('/operator/customer/{id}/destroy', [OperatorController::class, 'destroyCustomerOperator'])->name('operator.customer.destroy');
    Route::get('/operator/customer/{customer}/edit', [OperatorController::class, 'editCustomerOperator'])->name('operator.customer.edit');
    Route::put('/operator/customer/{customer}/update', [OperatorController::class, 'updateCustomerOperator'])->name('operator.customer.update');
    Route::get('/operator/autocomplete/customer', [OperatorController::class, 'autocomplete'])->name('operator.customer.autocomplete');

    Route::get('/operator/customer/activation', [OperatorController::class, 'indexActivationCustomerOperator'])->name('operator.customer.activation');
    Route::post('/operator/customer/activation/store', [OperatorController::class, 'storeActivationOperator'])->name('operator.customer.activation.store');

    Route::get('/operator/pacakge/view', [OperatorController::class, 'indexPackageOperator'])->name('operator.package.view');
    Route::post('/operator/package/store', [OperatorController::class, 'storePackageOperator'])->name('operator.package.store');
    Route::get('/operator/package/{id}/edit', [OperatorController::class, 'editPacakgeOperator'])->name('operator.package.edit');
    Route::put('/operator/package/{id}/update', [OperatorController::class, 'updatePackageOperator'])->name('operator.package.update');
    Route::delete('/operator/package/{id}/destroy', [OperatorController::class, 'destroyPackageOperator'])->name('operator.package.destroy');

    Route::get('/operator/invoice/view', [OperatorController::class, 'indexInvoiceOperator'])->name('operator.invoices.view');
    Route::get('/operator/invoices/{invoice}/edit', [OperatorController::class, 'editInvoiceOperator'])->name('operator.invoices.edit');
    Route::put('/operator/invoices/{invoice}', [OperatorController::class, 'updateInvoiceOperator'])->name('operator.invoices.update');
    Route::get('operator/invoices/search', [OperatorController::class, 'searchInvoiceOperator'])->name('operator.invoices.search');
    Route::delete('/operator/invoice/{invoice}', [OperatorController::class, 'destroyInvoiceOperator'])->name('operator.invoices.delete');

    Route::get('/operator/transaction/search', [OperatorController::class, 'searchTransactionsOperator'])->name('operator.transaction.search');
    Route::get('/operator/transaction/view', [OperatorController::class, 'indexTransactionsOperator'])->name('operator.transaction.view');

    Route::get('/operator/router/view', [OperatorController::class, 'indexOperatorRouter'])->name('operator.router.view');
    Route::post('/operator/router/connect', [OperatorController::class, 'connectOperatorRouter'])->name('operator.router.connect');
    Route::get('/operator/router/{id}/status', [OperatorController::class, 'getOperatorRouterStatus'])->name('operator.router.status');
    Route::delete('/operator/router/{router}/destroy', [OperatorController::class, 'destroyRouter'])->name('operator.destroy.router');

    Route::get('/operator/ip-pool/create', [OperatorController::class, 'createIpPool'])->name('operator.ip-pool.create');
    Route::post('/operator/ip-pool/store', [OperatorController::class, 'storeIpPool'])->name('operator.ip-pool.store');
    Route::delete('/operator/ip-pool/{id}/destroy', [OperatorController::class, 'destroyIpPool'])->name('operator.ip-pool.destroy');

    Route::get('/operator/ppp-profile/create', [OperatorController::class, 'createPppProfile'])->name('operator.ppp-profile.create');
    Route::post('/operator/ppp-profile/post', [OperatorController::class, 'storePppProfile'])->name('operator.ppp-profile.store');
    Route::delete('/operator/ppp-profile/{id}/remove', [OperatorController::class, 'removePppProfile'])->name('operator.ppp-profile.remove');

    Route::get('/operator/ppp-secret/create', [OperatorController::class, 'createPppSecretes'])->name('operator.ppp-secret.create');
    Route::post('/operator/ppp-secret/store', [OperatorController::class, 'storePppSecrets'])->name('operator.ppp-secret.store');
    Route::delete('/operator/ppp-secret/{id}/remove', [OperatorController::class, 'removePppSecrets'])->name('operator.ppp-secret.remove');
    Route::get('/operator/ppp-secret/search', [OperatorController::class, 'searchPppSecret'])->name('operator.ppp-secret.search');
    // ========================================== EXPORT DATA =========================================
    Route::get('export/customers', [ExportController::class, 'exportCustomerData'])->name('export.customers');
    Route::get('/export/invoices', [ExportController::class, 'exportInvoiceData'])->name('export.invoices');
});
Route::get('/dashboard', [DashboardController::class, 'index']);
