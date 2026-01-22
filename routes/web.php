<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StorageManagementController;
use App\Http\Controllers\StorageUnitController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\NonAuthController;
use App\Http\Controllers\ExpenseController;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentEmail;
use App\Http\Controllers\ReportController;

// ====================== Home & Auth ======================
Route::get('/', fn() => view('pages.home'))->name('homepage');
Route::get('/about', fn() => view('pages.about'))->name('about');
Route::get('/units-pricing', fn() => view('pages.unit'))->name('units.pricing');
Route::get('/faq', fn() => view('pages.faq'))->name('faq');
// Route::get('/booking', fn() => view('pages.customer-booking'))->name('booking');
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/online-booking', [NonAuthController::class, 'showBookingForm'])->name('online.booking.form');
Route::post('/online-booking', [NonAuthController::class, 'onlineBooking'])->name('online.booking');
Route::get('/booking', [NonAuthController::class, 'showAvailableStorage'])->name('show.storage');
Route::get('/booking-success/{bookingId}', [NonAuthController::class, 'bookingSuccess'])->name('booking.success');

Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('auth.login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('auth.logout');

// ====================== Dashboard ======================
Route::middleware('auth')->get('/dashboard', function () {
    return match(Auth::User()->role) {
        'admin' => redirect()->route('dashboard.admin'),
        default => abort(403),
    };
})->name('dashboard');

Route::middleware(['auth', 'role:admin'])->get('/dashboard/admin', [DashboardController::class, 'admin'])->name('dashboard.admin');

// ====================== Data Storage ======================
Route::prefix('data-storage')->name('data-storage.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [StorageController::class, 'index'])->name('index');
    Route::get('create', [StorageController::class, 'create'])->name('create');
    Route::post('/', [StorageController::class, 'store'])->name('store');
    Route::get('{data_storage}/edit', [StorageController::class, 'edit'])->name('edit');
    Route::put('{data_storage}', [StorageController::class, 'update'])->name('update');
    Route::delete('{data_storage}', [StorageController::class, 'destroy'])->name('destroy');
    Route::get('{data_storage}', [StorageController::class, 'show'])->name('show');
});

// ====================== Data Booking ======================
Route::prefix('data-booking')->name('data-booking.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [BookingController::class, 'index'])->name('index');
    Route::get('create', [BookingController::class, 'create'])->name('create');
    Route::post('/', [BookingController::class, 'store'])->name('store');
    Route::get('{booking}/edit', [BookingController::class, 'edit'])->name('edit');
    Route::put('{booking}', [BookingController::class, 'update'])->name('update');
    Route::delete('{booking}', [BookingController::class, 'destroy'])->name('destroy');
    Route::get('{booking}', [BookingController::class, 'show'])->name('show');
    Route::post('{id}/end', [BookingController::class, 'endBooking'])->name('end');
});

// ====================== Data Customer ======================
Route::prefix('data-customer')->name('data-customer.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('index');
    Route::get('create', [CustomerController::class, 'create'])->name('create');
    Route::post('/', [CustomerController::class, 'store'])->name('store');
    Route::get('{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
    Route::put('{customer}', [CustomerController::class, 'update'])->name('update');
    Route::delete('{customer}', [CustomerController::class, 'destroy'])->name('destroy');
    Route::get('{customer}', [CustomerController::class, 'show'])->name('show');
});

// ====================== Data Payment ======================
Route::prefix('data-payment')->name('data-payment.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [PaymentController::class, 'index'])->name('index');
    Route::get('{payment}', [PaymentController::class, 'show'])->name('show');
    Route::get('/payment/{id}/payment', [PaymentController::class, 'showPayment'])->name('payment');
    Route::post('/payment/{id}/payment/refresh', [PaymentController::class, 'refreshStatus'])->name('refresh-status');
    // Route::post('{id}', [PaymentController::class, 'refreshStatus'])->name('data-payment.refresh-status');
});

// ====================== Storage Management ======================
Route::prefix('storage-management')->name('storage-management.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [StorageManagementController::class, 'index'])->name('index');
    Route::get('create', [StorageManagementController::class, 'create'])->name('create');
    Route::post('/', [StorageManagementController::class, 'store'])->name('store');
    Route::get('{management}/edit', [StorageManagementController::class, 'edit'])->name('edit');
    Route::put('{management}', [StorageManagementController::class, 'update'])->name('update');
    Route::delete('{management}', [StorageManagementController::class, 'destroy'])->name('destroy');
    Route::get('{management}', [StorageManagementController::class, 'show'])->name('show');
    Route::put('{management}/last-clean', [StorageManagementController::class, 'lastClean'])->name('last-clean');
});

// ====================== Expenses & Report ======================
Route::prefix('expenses')->name('expenses.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [ExpenseController::class, 'index'])->name('index');
    Route::post('/', [ExpenseController::class, 'store'])->name('store');
    Route::put('{id}', [ExpenseController::class, 'update'])->name('update');
    Route::delete('{id}', [ExpenseController::class, 'destroy'])->name('destroy');
});

Route::prefix('report')->name('report.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('export-excel'); 
    Route::get('/export-pdf', [ReportController::class, 'exportPdf'])->name('export-pdf'); 
});

Route::post('midtrans-notification', [MidtransController::class, 'notification']);



// Route::get('/send-test-email', function () {
//     $paymentUrl = 'http://localhost:8120/payment-link';  // Ganti dengan URL pembayaran yang sesuai
//     Mail::to('alfintegu16@gmail.com')->send(new PaymentEmail($paymentUrl));
//     return 'Test email sent!';
// });
