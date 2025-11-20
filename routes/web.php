<?php

use App\Http\Controllers\Auth\PasswordResetController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryProductController;
use App\Http\Controllers\CompanyProfileController;
use App\Http\Controllers\CustomerProfileController;
use App\Http\Controllers\GoldPriceController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceItemController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductDetailController;
use App\Http\Controllers\OtherProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CreditNoteController;
use App\Http\Controllers\DebitNoteController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\CompanyValidationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RefundNoteController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SelfBilledInvoiceController;
use App\Http\Controllers\IsController;
use App\Http\Controllers\MsicController;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/', function () {
//     return view('dashboard.index');
// })->name('dashboard.index');

Auth::routes();

Route::get('/', function () {
    return view('auth/login');
})->name('auth.login');

Route::middleware('auth')->group(function () {

    // Dashboard Routes
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('home', [HomeController::class, 'index'])->name('home');

    // Company Profile Routes
    Route::get('company_profiles', [CompanyProfileController::class, 'index'])->name('company_profiles.index');
    Route::get('company_profiles/create', [CompanyProfileController::class, 'create'])->name('company_profiles.create');
    Route::post('company_profiles', [CompanyProfileController::class, 'store'])->name('company_profiles.store');
    Route::get('company_profiles/{id}/edit', [CompanyProfileController::class, 'edit'])->name('company_profiles.edit');
    Route::patch('company_profiles/{id}', [CompanyProfileController::class, 'update'])->name('company_profiles.update');
    Route::get('company_profiles/{id}', [CompanyProfileController::class, 'show'])->name('company_profiles.show');
    Route::delete('company_profiles/{id}', [CompanyProfileController::class, 'destroy'])->name('company_profiles.destroy');
    Route::get('company_profiles/{id}/view', [CompanyProfileController::class, 'view'])->name('company_profiles.view');
    Route::post('search', [CompanyProfileController::class, 'search'])->name('company_profiles.search');

    // Category Product Routes
    Route::get('category_products', [CategoryProductController::class, 'index'])->name('category_products.index');
    Route::get('category_products/create', [CategoryProductController::class, 'create'])->name('category_products.create');
    Route::post('category_products', [CategoryProductController::class, 'store'])->name('category_products.store');
    Route::get('category_products/{id}/edit', [CategoryProductController::class, 'edit'])->name('category_products.edit');
    Route::patch('category_products/{id}', [CategoryProductController::class, 'update'])->name('category_products.update');
    Route::get('category_products/{id}', [CategoryProductController::class, 'show'])->name('category_products.show');
    Route::delete('category_products/{id}', [CategoryProductController::class, 'destroy'])->name('category_products.destroy');
    Route::get('category_products/{id}/view', [CategoryProductController::class, 'view'])->name('category_products.view');
    Route::post('search', [CategoryProductController::class, 'search'])->name('category_products.search');

    // Customer Profile Routes
    Route::get('customer_profiles', [CustomerProfileController::class, 'index'])->name('customer_profiles.index');
    Route::get('customer_profiles/create', [CustomerProfileController::class, 'create'])->name('customer_profiles.create');
    Route::post('customer_profiles', [CustomerProfileController::class, 'store'])->name('customer_profiles.store');
    Route::get('customer_profiles/{id}/edit', [CustomerProfileController::class, 'edit'])->name('customer_profiles.edit');
    Route::patch('customer_profiles/{id}', [CustomerProfileController::class, 'update'])->name('customer_profiles.update');
    Route::get('customer_profiles/{id}', [CustomerProfileController::class, 'show'])->name('customer_profiles.show');
    Route::delete('customer_profiles/{id}', [CustomerProfileController::class, 'destroy'])->name('customer_profiles.destroy');
    Route::get('customer_profiles/{id}/view', [CustomerProfileController::class, 'view'])->name('customer_profiles.view');
    Route::post('search', [CustomerProfileController::class, 'search'])->name('customer_profiles.search');

    // Gold Price Routes
    Route::get('gold_prices', [GoldPriceController::class, 'index'])->name('gold_prices.index');
    Route::get('gold_prices/create', [GoldPriceController::class, 'create'])->name('gold_prices.create');
    Route::post('gold_prices', [GoldPriceController::class, 'store'])->name('gold_prices.store');
    Route::get('gold_prices/{id}/edit', [GoldPriceController::class, 'edit'])->name('gold_prices.edit');
    Route::patch('gold_prices/{id}', [GoldPriceController::class, 'update'])->name('gold_prices.update');
    Route::get('gold_prices/{id}', [GoldPriceController::class, 'show'])->name('gold_prices.show');
    Route::delete('gold_prices/{id}', [GoldPriceController::class, 'destroy'])->name('gold_prices.destroy');
    Route::get('gold_prices/{id}/view', [GoldPriceController::class, 'view'])->name('gold_prices.view');
    Route::post('search', [GoldPriceController::class, 'search'])->name('gold_prices.search');

    // Invoice Item Routes
    Route::get('invoice_items', [InvoiceItemController::class, 'index'])->name('invoice_items.index');
    Route::get('invoice_items/create', [InvoiceItemController::class, 'create'])->name('invoice_items.create');
    Route::post('invoice_items', [InvoiceItemController::class, 'store'])->name('invoice_items.store');
    Route::get('invoice_items/{id}/edit', [InvoiceItemController::class, 'edit'])->name('invoice_items.edit');
    Route::patch('invoice_items/{id}', [InvoiceItemController::class, 'update'])->name('invoice_items.update');
    Route::get('invoice_items/{id}', [InvoiceItemController::class, 'show'])->name('invoice_items.show');
    Route::delete('invoice_items/{id}', [InvoiceItemController::class, 'destroy'])->name('invoice_items.destroy');
    Route::get('invoice_items/{id}/view', [InvoiceItemController::class, 'view'])->name('invoice_items.view');
    Route::post('search', [InvoiceItemController::class, 'search'])->name('invoice_items.search');

    // Invoice Routes
    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('invoices/{id}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
    Route::patch('invoices/{id}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::get('invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::delete('invoices/{id}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::get('invoices/{id}/view', [InvoiceController::class, 'view'])->name('invoices.view');
    Route::post('search', [InvoiceController::class, 'search'])->name('invoices.search');

    // Invoice Payment Routes
    Route::get('/invoices/{id}/payments', [InvoiceController::class, 'payments'])->name('invoices.payments');

    // Invoice Print Routes
    Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');

    // Payment Routes
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('payments/{id}/edit', [PaymentController::class, 'edit'])->name('payments.edit');
    Route::patch('payments/{id}', [PaymentController::class, 'update'])->name('payments.update');
    Route::get('payments/{id}', [PaymentController::class, 'show'])->name('payments.show');
    Route::delete('payments/{id}', [PaymentController::class, 'destroy'])->name('payments.destroy');
    Route::get('payments/{id}/view', [PaymentController::class, 'view'])->name('payments.view');
    Route::post('search', [PaymentController::class, 'search'])->name('payments.search');

    // Receipt Routes
    Route::get('receipts', [ReceiptController::class, 'index'])->name('receipts.index');
    Route::get('receipts/create', [ReceiptController::class, 'create'])->name('receipts.create');
    Route::post('receipts', [ReceiptController::class, 'store'])->name('receipts.store');
    Route::get('receipts/{id}/edit', [ReceiptController::class, 'edit'])->name('receipts.edit');
    Route::patch('receipts/{id}', [ReceiptController::class, 'update'])->name('receipts.update');
    Route::get('receipts/{id}', [ReceiptController::class, 'show'])->name('receipts.show');
    Route::delete('receipts/{id}', [ReceiptController::class, 'destroy'])->name('receipts.destroy');
    Route::get('receipts/{id}/view', [ReceiptController::class, 'view'])->name('receipts.view');
    Route::post('search', [ReceiptController::class, 'search'])->name('receipts.search');

    // Product Detail Routes
    Route::get('product_details', [ProductDetailController::class, 'index'])->name('product_details.index');
    Route::get('product_details/create', [ProductDetailController::class, 'create'])->name('product_details.create');
    Route::post('product_details', [ProductDetailController::class, 'store'])->name('product_details.store');
    Route::get('product_details/{id}/edit', [ProductDetailController::class, 'edit'])->name('product_details.edit');
    Route::patch('product_details/{id}', [ProductDetailController::class, 'update'])->name('product_details.update');
    Route::get('product_details/{id}', [ProductDetailController::class, 'show'])->name('product_details.show');
    Route::delete('product_details/{id}', [ProductDetailController::class, 'destroy'])->name('product_details.destroy');
    Route::get('product_details/{id}/view', [ProductDetailController::class, 'view'])->name('product_details.view');
    Route::post('search', [ProductDetailController::class, 'search'])->name('product_details.search');

    // Other Profile Routes
    Route::get('others/create', [OtherProfileController::class, 'create'])->name('others.create');
    Route::post('/others/store', [OtherProfileController::class, 'store'])->name('others.store');
    Route::post('/other-profiles', [OtherProfileController::class, 'store'])->name('other-profiles.store');
    Route::get('others/{id}/edit', [OtherProfileController::class, 'edit'])->name('others.edit');
    Route::patch('others/{id}', [OtherProfileController::class, 'update'])->name('others.update');
    Route::get('others/{id}', [OtherProfileController::class, 'destroy'])->name('others.destroy');
    Route::post('/others/store-ajax', [OtherProfileController::class, 'storeAjax'])->name('others.store-ajax');

    // Credit Note Routes
    Route::get('credit_notes', [CreditNoteController::class, 'index'])->name('credit_notes.index');
    Route::get('credit_notes/create', [CreditNoteController::class, 'create'])->name('credit_notes.create');
    Route::post('credit_notes', [CreditNoteController::class, 'store'])->name('credit_notes.store');
    Route::get('credit_notes/{id}/edit', [CreditNoteController::class, 'edit'])->name('credit_notes.edit');
    Route::patch('credit_notes/{id}', [CreditNoteController::class, 'update'])->name('credit_notes.update');
    Route::get('credit_notes/{id}', [CreditNoteController::class, 'show'])->name('credit_notes.show');
    Route::delete('credit_notes/{id}', [CreditNoteController::class, 'destroy'])->name('credit_notes.destroy');
    Route::get('credit_notes/{id}/view', [CreditNoteController::class, 'view'])->name('credit_notes.view');

    // Get Invoice Details - Credit Note From Page 
    // Guna dekat JS
    Route::get(
        'credit_notes/get-invoice-details/{invoice_no}',
        [CreditNoteController::class, 'getInvoiceDetails']
    )
        ->name('credit_notes.get-invoice-details');

    // debit Note Routes
    Route::get('debit_notes', [DebitNoteController::class, 'index'])->name('debit_notes.index');
    Route::get('debit_notes/create', [DebitNoteController::class, 'create'])->name('debit_notes.create');
    Route::post('debit_notes', [DebitNoteController::class, 'store'])->name('debit_notes.store');
    Route::get('debit_notes/{id}/edit', [DebitNoteController::class, 'edit'])->name('debit_notes.edit');
    Route::patch('debit_notes/{id}', [DebitNoteController::class, 'update'])->name('debit_notes.update');
    Route::get('debit_notes/{id}', [DebitNoteController::class, 'show'])->name('debit_notes.show');
    Route::delete('debit_notes/{id}', [DebitNoteController::class, 'destroy'])->name('debit_notes.destroy');
    Route::get('debit_notes/{id}/view', [DebitNoteController::class, 'view'])->name('debit_notes.view');

    // Get Invoice Details - debit Note From Page 
    // Guna dekat JS
    Route::get(
        'debit_notes/get-invoice-details/{invoice_no}',
        [DebitNoteController::class, 'getInvoiceDetails']
    )
        ->name('debit_notes.get-invoice-details');

    // refund Note Routes
    Route::get('refund_notes', [RefundNoteController::class, 'index'])->name('refund_notes.index');
    Route::get('refund_notes/create', [RefundNoteController::class, 'create'])->name('refund_notes.create');
    Route::post('refund_notes', [RefundNoteController::class, 'store'])->name('refund_notes.store');
    Route::get('refund_notes/{id}/edit', [RefundNoteController::class, 'edit'])->name('refund_notes.edit');
    Route::patch('refund_notes/{id}', [RefundNoteController::class, 'update'])->name('refund_notes.update');
    Route::get('refund_notes/{id}', [RefundNoteController::class, 'show'])->name('refund_notes.show');
    Route::delete('refund_notes/{id}', [RefundNoteController::class, 'destroy'])->name('refund_notes.destroy');
    Route::get('refund_notes/{id}/view', [RefundNoteController::class, 'view'])->name('refund_notes.view');

    // Get Invoice Details - refund Note From Page 
    // Guna dekat JS
    Route::get(
        'refund_notes/get-invoice-details/{invoice_no}',
        [RefundNoteController::class, 'getInvoiceDetails']
    )
        ->name('refund_notes.get-invoice-details');

    // TO CHECK IF COMPANY NAME ALREADY EXISTS IN CUSTOMER PROFILE OR OTHER PROFILE
    // Guna dekat JS di Invoice form.blade.php
    Route::post('/check-company-name', [CompanyValidationController::class, 'checkCompanyName']);

    Route::get('/forget_password', [PasswordResetController::class, 'forget_password'])->name('password.forget_password');
    Route::post('/temp_password', [PasswordResetController::class, 'temp_password'])->name('password.temp');
    Route::get('/change_password', [PasswordResetController::class, 'change_password'])->name('password.change');
    Route::post('/save_password', [PasswordResetController::class, 'save_password'])->name('password.save');

    // Get Invoice Details - Invoice From Page for payment form
    Route::get('/invoices/{invoice}/details', [App\Http\Controllers\InvoiceController::class, 'getDetails'])->name('invoices.details');

    // Report Routes
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/sales', [ReportController::class, 'getSalesReport'])->name('reports.sales');
    Route::get('reports/print', [ReportController::class, 'printSalesReport'])->name('reports.print');
    Route::get('reports/company', [ReportController::class, 'companyReport'])->name('reports.company');
    Route::get('reports/company/sales', [ReportController::class, 'getCompanySalesReport'])->name('reports.company.sales');
    Route::get('reports/company/print', [ReportController::class, 'printCompanySalesReport'])->name('reports.company.print');
    Route::get('reports/is', [ReportController::class, 'isReport'])->name('reports.is');
    Route::get('reports/is/generate', [ReportController::class, 'getIsReport'])->name('reports.is.generate');
    Route::get('reports/is/print', [ReportController::class, 'printIsReport'])->name('reports.is.print');

    // Balance Adjustment Routes
    Route::get('reports/balance-adjustments', [ReportController::class, 'balanceAdjustments'])->name('reports.balance-adjustments');
    Route::get('reports/balance-adjustments/create', [ReportController::class, 'createBalanceAdjustment'])->name('reports.balance-adjustments.create');
    Route::post('reports/balance-adjustments', [ReportController::class, 'storeBalanceAdjustment'])->name('reports.balance-adjustments.store');
    Route::get('reports/balance-adjustments/{id}/edit', [ReportController::class, 'editBalanceAdjustment'])->name('reports.balance-adjustments.edit');
    Route::patch('reports/balance-adjustments/{id}', [ReportController::class, 'updateBalanceAdjustment'])->name('reports.balance-adjustments.update');
    Route::delete('reports/balance-adjustments/{id}', [ReportController::class, 'destroyBalanceAdjustment'])->name('reports.balance-adjustments.destroy');

    // Self Billed Invoice Routes
    Route::get('self_billed_invoices', [SelfBilledInvoiceController::class, 'index'])->name('self_billed_invoices.index');
    Route::get('self_billed_invoices/create', [SelfBilledInvoiceController::class, 'create'])->name('self_billed_invoices.create');
    Route::post('self_billed_invoices', [SelfBilledInvoiceController::class, 'store'])->name('self_billed_invoices.store');
    Route::get('self_billed_invoices/{id}/edit', [SelfBilledInvoiceController::class, 'edit'])->name('self_billed_invoices.edit');
    Route::patch('self_billed_invoices/{id}', [SelfBilledInvoiceController::class, 'update'])->name('self_billed_invoices.update');
    Route::get('self_billed_invoices/{id}', [SelfBilledInvoiceController::class, 'show'])->name('self_billed_invoices.show');
    Route::delete('self_billed_invoices/{id}', [SelfBilledInvoiceController::class, 'destroy'])->name('self_billed_invoices.destroy');
    Route::get('self_billed_invoices/{id}/view', [SelfBilledInvoiceController::class, 'view'])->name('self_billed_invoices.view');
    Route::post('search', [SelfBilledInvoiceController::class, 'search'])->name('self_billed_invoices.search');
    
    // IS Routes
    Route::get('is', [IsController::class, 'index'])->name('is.index');
    Route::get('is/create', [IsController::class, 'create'])->name('is.create');
    Route::post('is', [IsController::class, 'store'])->name('is.store');
    Route::get('is/{id}/edit', [IsController::class, 'edit'])->name('is.edit');
    Route::patch('is/{id}', [IsController::class, 'update'])->name('is.update');
    Route::get('is/{id}', [IsController::class, 'show'])->name('is.show');
    Route::delete('is/{id}', [IsController::class, 'destroy'])->name('is.destroy');
    Route::get('is/{id}/view', [IsController::class, 'view'])->name('is.view');
    Route::post('search', [IsController::class, 'search'])->name('is.search');

      // MSIC Routes
    Route::get('msics', [MsicController::class, 'index'])->name('msics.index');
    Route::get('msics/create', [MsicController::class, 'create'])->name('msics.create');
    Route::post('msics', [MsicController::class, 'store'])->name('msics.store');
    Route::get('msics/{id}/edit', [MsicController::class, 'edit'])->name('msics.edit');
    Route::patch('msics/{id}', [MsicController::class, 'update'])->name('msics.update');
    Route::get('msics/{id}', [MsicController::class, 'show'])->name('msics.show');
    Route::delete('msics/{id}', [MsicController::class, 'destroy'])->name('msics.destroy');
    Route::get('msics/{id}/view', [MsicController::class, 'view'])->name('msics.view');
  
});
