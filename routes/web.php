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

    Route::get('/user/manager', [DashboardController::class, 'manager'])->name('user.manager');
    Route::get('/dashboard/admin', [AdminController::class, 'dashboard'])->name('dashboard.admin');

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
    Route::get('/finance/dashboard', [FinanceController::class, 'dashboard'])->name('finance.dashboard');
    Route::get('/finance/list/customer', [FinanceController::class, 'getAllInvoices'])->name('finance.list.customer');
    Route::get('/finance/transactions/history', [FinanceController::class, 'transactionsHistory'])->name('finance.transactions.history');
    Route::get('/finance/data/customers', [FinanceController::class, 'getCustomerData'])->name('finance.data.customers');
    Route::get('/finance/report', [FinanceController::class, 'activityReport'])->name('finance.report');
    Route::get('/finance/customer/arrears', [FinanceController::class, 'customerArrears'])->name('finance.customer.arrears');

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

    Route::get('admin/transaction/search', [AdminController::class, 'searchTransactionsAdmin'])->name('admin.transaction.search');
    Route::get('admin/transaction/view', [AdminController::class, 'indexTransactionsAdmin'])->name('admin.transaction.view');

    Route::get('/admin/customer/arrears', [AdminController::class, 'adminCustomerArrears'])->name('admin.customer.arrears');

    Route::get('/admin/data/customers', [AdminController::class, 'adminGetCustomerData'])->name('admin.data.customers');

    // OPERATOR =====================
    Route::get('/operator/dashboard', [OperatorController::class, 'dashboard'])->name('operator.dashboard');
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

    Route::get('operator/invoice/view', [OperatorController::class, 'indexInvoiceOperator'])->name('operator.invoices.view');
    Route::get('operator/invoices/{invoice}/edit', [OperatorController::class, 'editInvoiceOperator'])->name('operator.invoices.edit');
    Route::put('operator/invoices/{invoice}', [OperatorController::class, 'updateInvoiceOperator'])->name('operator.invoices.update');
    Route::get('operator/invoices/search', [OperatorController::class, 'searchInvoiceOperator'])->name('operator.invoices.search');
    Route::delete('operator/invoice/{invoice}', [OperatorController::class, 'destroyInvoiceOperator'])->name('operator.invoices.delete');

    Route::get('operator/transaction/search', [OperatorController::class, 'searchTransactionsOperator'])->name('operator.transaction.search');
    Route::get('operator/transaction/view', [OperatorController::class, 'indexTransactionsOperator'])->name('operator.transaction.view');
});
Route::get('/dashboard', [DashboardController::class, 'index']);
