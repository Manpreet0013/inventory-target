<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;

// Dashboards
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Inventory\DashboardController as InventoryDashboard;
use App\Http\Controllers\Executive\DashboardController as ExecutiveDashboard;
use App\Http\Controllers\Accountant\DashboardController as AccountantDashboard;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Authenticated Common Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /* Profile */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /* Role-based profile (optional) */
    Route::get('{role}/profile', [ProfileController::class, 'edit'])->name('role.profile');
    Route::patch('{role}/profile', [ProfileController::class, 'update'])->name('role.profile.update');

    /* Notifications */
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::get('/notifications/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])
        ->name('notifications.markAsRead');
});

/*
|--------------------------------------------------------------------------
| Dashboard Redirect
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->get('/dashboard-redirect', function () {
    $user = auth()->user();

    return match (true) {
        $user->hasRole('Admin')             => redirect('/admin/dashboard'),
        $user->hasRole('Inventory Manager') => redirect('/inventory/dashboard'),
        $user->hasRole('Executive')         => redirect('/executive/report'),
        $user->hasRole('Accountant')        => redirect('/accountant/dashboard'),
        default                             => redirect('/dashboard'),
    };
});

Route::middleware(['auth', 'verified'])
    ->get('/dashboard', fn () => redirect('/dashboard-redirect'))
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Admin'])
    ->prefix('admin')
    ->group(function () {

        Route::get('/dashboard', [AdminDashboard::class, 'index']);

        Route::get('/products', [AdminDashboard::class, 'products'])->name('admin.add-product');
        Route::post('/products/store', [AdminDashboard::class, 'storeProduct']);

        Route::get('/targets', [AdminDashboard::class, 'targets'])->name('admin.targets');
        Route::post('/targets/store', [AdminDashboard::class, 'storeTarget']);
        Route::get('/target-listing', [AdminDashboard::class, 'targets_listing'])->name('admin.list');

        Route::get('/product-listing', [AdminDashboard::class, 'product_listing'])->name('admin.products');
        Route::get('/product-listing/{id}', [AdminDashboard::class, 'productDetails'])
            ->name('admin.products.details');

        Route::get(
            '/product-listing/{product}/sales/{target}',
            [AdminDashboard::class, 'saleListing']
        )->name('admin.sales.details');

        /* Companies */
        Route::resource('companies', \App\Http\Controllers\Admin\CompanyController::class)
            ->names('admin.companies');

        /* Users */
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)
            ->names('admin.users');

        Route::get('users/{user}/profile',
            [\App\Http\Controllers\Admin\UserController::class, 'profile']
        )->name('admin.users.profile');

        Route::get(
            'users/{user}/report',
            [\App\Http\Controllers\Admin\UserReportController::class, 'show']
        )->name('admin.users.report');
    });

/*
|--------------------------------------------------------------------------
| Inventory Manager Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Inventory Manager'])
    ->prefix('inventory')
    ->group(function () {

        Route::get('/dashboard', [InventoryDashboard::class, 'index'])
            ->name('inventory.dashboard');

        Route::post('/notify/{id}', [InventoryDashboard::class, 'notifyAdmin']);
    });

/*
|--------------------------------------------------------------------------
| Executive Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Executive'])
    ->prefix('executive')
    ->group(function () {

        Route::get('/dashboard', [ExecutiveDashboard::class, 'index'])
            ->name('executive.dashboard');

        Route::get('/report', [ExecutiveDashboard::class, 'report'])
            ->name('executive.report');

        Route::get('/target/{target}', [ExecutiveDashboard::class, 'show'])
            ->name('executive.target.show');

        Route::get('/target/{target}/sales', [ExecutiveDashboard::class, 'sales'])
            ->name('executive.target.sales');

        Route::post('/target/{target}/accept', [ExecutiveDashboard::class, 'accept'])
            ->name('executive.target.accept');

        Route::post('/target/{target}/reject', [ExecutiveDashboard::class, 'reject'])
            ->name('executive.target.reject');

        Route::post('/target/{target}/accept-partial', [ExecutiveDashboard::class, 'acceptPartial'])
            ->name('executive.target.accept-partial');

        Route::post('/target/{target}/reassign', [ExecutiveDashboard::class, 'reassign'])
            ->name('executive.target.reassign');

        Route::post('/target/{target}/split', [ExecutiveDashboard::class, 'split'])
            ->name('executive.target.split');

        Route::get('/target/{target}/split-view', [ExecutiveDashboard::class, 'splitView'])
            ->name('executive.target.split.view');

        Route::get('/targets/managed', [ExecutiveDashboard::class, 'managedTargets'])
            ->name('executive.targets.managed');

        Route::get('/targets/assigned', [ExecutiveDashboard::class, 'assignedTargets'])
            ->name('executive.targets.assigned');

        Route::get('/sale/create/{target}', [ExecutiveDashboard::class, 'createSale'])
            ->name('executive.sale.create');

        Route::post('/sale/store', [ExecutiveDashboard::class, 'storeSale'])
            ->name('executive.sale.store');
    });

/*
|--------------------------------------------------------------------------
| Accountant Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Accountant'])
    ->prefix('accountant')
    ->group(function () {

        Route::get('/dashboard', [AccountantDashboard::class, 'index']);

        Route::post('/sale/{sale}/approve', [AccountantDashboard::class, 'approve']);
        Route::post('/sale/{sale}/reject', [AccountantDashboard::class, 'reject']);
    });

require __DIR__.'/auth.php';
