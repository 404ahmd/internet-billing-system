<?php

use App\Http\Controllers\ActivationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OperatorController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Constraint\Operator;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

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
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware(['auth'])->group(function () {

    Route::get('/dump', function () {
        return view('operator.dum_content');
    })->name('dump');

    Route::get('/user/operator', [DashboardController::class, 'operator'])->name('user.operator');
    Route::get('/user/finance', [DashboardController::class, 'finance'])->name('user.finance');
    Route::get('/user/manager', [DashboardController::class, 'manager'])->name('user.manager');
    Route::get('/dashboard/admin', [AdminController::class, 'dashboard'])->name('dashboard.admin');
    Route::get('/dashboard/finance', [FinanceController::class, 'dashboard'])->name('dashboard.finance');

    //CUSTOMER =======================
    // Route::get('/customer/view', [CustomerController::class, 'index'])->name('customer.view');
    // Route::post('/customer/store', [CustomerController::class, 'store'])->name('customer.store');
    // Route::get('/customer/search', [CustomerController::class, 'search'])->name('customer.search');
    // Route::delete('/customer/{id}/destroy', [CustomerController::class, 'destroy'])->name('customer.destroy');
    // Route::get('/customer/{customer}/edit', [CustomerController::class, 'edit'])->name('customer.edit');
    // Route::put('/customer/{customer}/update', [CustomerController::class, 'update'])->name('customer.update');

    //AUTOCOMPLETE ======================
    Route::get('/autocomplete/customer', [CustomerController::class, 'autocomplete'])->name('customer.autocomplete');

    //ACTIVATION =======================
    // Route::get('/customer/activation', [ActivationController::class, 'index'])->name('customer.activation');
    // Route::post('/customer/activation/store', [ActivationController::class, 'store'])->name('customer.activation.store');


    //PACKAGE =======================
    // Route::get('/pacakge/view', [PackageController::class, 'index'])->name('package.view');
    // Route::post('/package/store', [PackageController::class, 'store'])->name('package.store');
    // Route::get('/package/{id}/edit', [PackageController::class, 'edit'])->name('package.edit');
    // Route::put('/package/{id}/update', [PackageController::class, 'update'])->name('package.update');
    // Route::delete('/package/{id}/destroy', [PackageController::class, 'destroy'])->name('package.destroy');

    //INVOICE =======================
    // Route::get('/invoice/view', [InvoiceController::class, 'index'])->name('invoice.view');
    // Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
    // Route::put('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
    // Route::get('/invoices/search', [InvoiceController::class, 'search'])->name('invoices.search');
    // Route::delete('/invoice/{invoice}', [InvoiceController::class, 'destroy'])->name('invoice.delete');
    // Route::get('/autocomplete/invoice', [InvoiceController::class, 'autocomplete'])->name('invoice.autocomplete');

    //TRANSACTION =======================
    // Route::get('/transaction/view', [TransactionController::class, 'index'])->name('transaction.view');
    // Route::get('/transaction/search', [TransactionController::class, 'search'])->name('transaction.search');

    //FINANACE===========================
    Route::get('/finance/list/customer', [FinanceController::class, 'getAllInvoices'])->name('finance.list.customer');
    Route::get('/finance/transactions/history', [FinanceController::class, 'transactionsHistory'])->name('finance.transactions.history');
    Route::get('/finance/data/customers', [FinanceController::class, 'getCustomerData'])->name('finance.data.customers');
    Route::get('/finance/report', [FinanceController::class, 'activityReport'])->name('finance.report');
    Route::get('/finance/customer/arrears', [FinanceController::class, 'customerArrears'])->name('finance.customer.arrears');

    //ADMININSTRATOR======================
    Route::get('/dashboard/operator', [AdminController::class, 'dashboard'])->name('dashboard.operator');
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
    Route::get('admin/invoices/{invoice}/edit', [AdminController::class, 'editInvoiceadmin'])->name('admin.invoices.edit');
    Route::put('admin/invoices/{invoice}', [AdminController::class, 'updateInvoiceadmin'])->name('admin.invoices.update');
    Route::get('admin/invoices/search', [AdminController::class, 'searchInvoiceadmin'])->name('admin.invoices.search');
    Route::delete('admin/invoice/{invoice}', [AdminController::class, 'destroyInvoiceadmin'])->name('admin.invoice.delete');

    Route::get('admin/transaction/search', [AdminController::class, 'searchTransactionsAdmin'])->name('admin.transaction.search');
    Route::get('admin/transaction/view', [AdminController::class, 'indexTransactionsAdmin'])->name('admin.transaction.view');
    Route::get('/admin/customer/arrears', [AdminController::class, 'adminCustomerArrears'])->name('admin.customer.arrears');

    Route::get('/admin/data/customers', [AdminController::class, 'adminGetCustomerData'])->name('admin.data.customers');


});
// Route::get('/home', function () {
//     return redirect('/dashboard');
// });

Route::get('/dashboard', [DashboardController::class, 'index']);
