<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IncomeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DebtController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SitemapController;

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\PremiumController;
use App\Http\Controllers\Admin\AppearanceController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\SecurityController;
use App\Http\Controllers\Admin\AccountController;

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments');
    Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics');
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports');
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');
    Route::get('/premium', [PremiumController::class, 'index'])->name('premium');
    Route::get('/appearance', [AppearanceController::class, 'index'])->name('appearance');
    Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings');
    Route::get('/security', [SecurityController::class, 'index'])->name('security');
    Route::get('/account', [AccountController::class, 'index'])->name('account');
});

use App\Http\Controllers\User\SettingsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/expenses/add', [ExpenseController::class, 'create'])->name('expenses.create');
    Route::get('/income/add', [IncomeController::class, 'create'])->name('income.create');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');
    
    Route::get('/budget', [BudgetController::class, 'index'])->name('budget.index');
    Route::post('/budget', [BudgetController::class, 'store'])->name('budget.store');
    Route::delete('/budget/{budget}', [BudgetController::class, 'destroy'])->name('budget.destroy');

    Route::get('/reminders', [ReminderController::class, 'index'])->name('reminders.index');
    Route::post('/reminders', [ReminderController::class, 'store'])->name('reminders.store');
    Route::post('/reminders/{reminder}/toggle', [ReminderController::class, 'toggle'])->name('reminders.toggle');
    Route::delete('/reminders/{reminder}', [ReminderController::class, 'destroy'])->name('reminders.destroy');

    Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::post('/reset-data', [SettingsController::class, 'resetData'])->name('reset-data');
        Route::get('/security', function () { return view('user.settings.security'); })->name('security');
        Route::get('/preferences', function () { return view('user.settings.preferences'); })->name('preferences');
        Route::get('/instructions', function () { return view('user.settings.instructions'); })->name('instructions');
        Route::get('/legal', function () { return view('user.settings.legal'); })->name('legal');
    });

    Route::get('/history', [HistoryController::class, 'index'])->name('history.index');

    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

    Route::get('/debts', [DebtController::class, 'index'])->name('debts.index');
    Route::post('/debts', [DebtController::class, 'store'])->name('debts.store');
    Route::post('/debts/{debt}/pay', [DebtController::class, 'pay'])->name('debts.pay');
    Route::post('/debts/{debt}/mark-as-paid', [DebtController::class, 'markAsPaid'])->name('debts.paid');
    Route::delete('/debts/{debt}', [DebtController::class, 'destroy'])->name('debts.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
