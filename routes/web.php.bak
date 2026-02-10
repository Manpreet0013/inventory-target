<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Inventory\DashboardController as InventoryDashboard;
use App\Http\Controllers\Executive\DashboardController as ExecutiveDashboard;
use App\Http\Controllers\Accountant\DashboardController as AccountantDashboard;


use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
     Route::get('{role}/profile', [ProfileController::class, 'edit'])
        ->name('role.profile');

    Route::patch('{role}/profile', [ProfileController::class, 'update'])
        ->name('role.profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
    ->name('profile.destroy');

    // Mark a notification as read
    Route::get('/notifications/mark-as-read/{id}', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])
        ->name('notifications.markAsRead');

    // Optional: view all notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])
        ->name('notifications.index');
});

Route::middleware(['auth'])->get('/dashboard-redirect', function () {
    $user = auth()->user();

    if ($user->hasRole('Admin')) {
        return redirect('/admin/dashboard'); // /admin/dashboard
    }

    if ($user->hasRole('Inventory Manager')) {
        return redirect('/inventory/dashboard');
    }

    if ($user->hasRole('Executive')) {
        return redirect('/executive/dashboard');
    }

    if ($user->hasRole('Accountant')) {
        return redirect('/accountant/dashboard');
    }

    return redirect('/dashboard'); // fallback
});


Route::middleware(['auth', 'role:Admin'])->prefix('admin')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/products', [DashboardController::class, 'products'])->name('admin.add-product');
    Route::post('/products/store', [DashboardController::class, 'storeProduct']);

    Route::get('/target-listing', [DashboardController::class, 'targets_listing'])->name('admin.list');
    Route::get('/targets', [DashboardController::class, 'targets'])->name('admin.targets');
    Route::post('/targets/store', [DashboardController::class, 'storeTarget']);

    Route::get('/product-listing', [DashboardController::class, 'product_listing'])->name('admin.products');
    Route::get('/product-listing/{id}', [DashboardController::class, 'productDetails'])->name('admin.products.details');


    Route::get(
            '/product-listing/{product}/sales/{target}',
            [DashboardController::class, 'saleListing']
        )->name('admin.sales.details');

     // Company resource
    Route::resource('companies', \App\Http\Controllers\Admin\CompanyController::class)
         ->names([
             'index' => 'admin.companies.index',
             'create' => 'admin.companies.create',
             'store' => 'admin.companies.store',
             'edit' => 'admin.companies.edit',
             'update' => 'admin.companies.update',
             'destroy' => 'admin.companies.destroy'
         ]);

    // User resource
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)
         ->names([
             'index' => 'admin.users.index',
             'create' => 'admin.users.create',
             'store' => 'admin.users.store',
             'edit' => 'admin.users.edit',
             'update' => 'admin.users.update',
             'destroy' => 'admin.users.destroy'
         ]);

    // Optional: Admin view user profile
    Route::get('users/{user}/profile', [\App\Http\Controllers\Admin\UserController::class, 'profile'])
         ->name('admin.users.profile');

});

Route::middleware(['auth','role:Inventory Manager'])
    ->prefix('inventory')
    ->group(function () {

        Route::get('/dashboard', [InventoryDashboard::class, 'index']);
        Route::post('/notify/{id}', [InventoryDashboard::class, 'notifyAdmin']);
});

Route::middleware(['auth','role:Executive'])
    ->prefix('executive')
    ->group(function () {

        Route::get('/dashboard', [ExecutiveDashboard::class, 'index'])->name('executive.dashboard');

        // Add names to these
        Route::post('/target/{target}/accept', [ExecutiveDashboard::class, 'accept'])
             ->name('executive.target.accept');

        Route::post('/target/{target}/reject', [ExecutiveDashboard::class, 'reject'])
             ->name('executive.target.reject');

        Route::post('/target/{target}/accept-partial', [ExecutiveDashboard::class, 'acceptPartial'])
            ->name('executive.target.accept-partial');

        Route::get('/target/{target}', [ExecutiveDashboard::class, 'show'])
             ->name('executive.target.show');

        Route::get('/sale/create/{target}', [ExecutiveDashboard::class, 'createSale'])
            ->name('executive.sale.create');

        Route::post('/sale/store', [ExecutiveDashboard::class, 'storeSale'])
            ->name('executive.sale.store');


        Route::post('/target/{target}/reassign', [ExecutiveDashboard::class, 'reassign'])
             ->name('executive.target.reassign');

        Route::post('/target/{target}/split', [ExecutiveDashboard::class, 'split'])
            ->name('executive.target.split');

        Route::get('/report', [ExecutiveDashboard::class, 'report'])
            ->name('executive.report');

});


Route::middleware(['auth','role:Accountant'])
    ->prefix('accountant')
    ->group(function () {

        Route::get('/dashboard', [AccountantDashboard::class, 'index']);
        Route::post('/sale/{sale}/approve', [AccountantDashboard::class, 'approve']);
        Route::post('/sale/{sale}/reject', [AccountantDashboard::class, 'reject']);
});
require __DIR__.'/auth.php';

