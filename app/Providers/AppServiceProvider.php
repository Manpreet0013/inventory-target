<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.admin', function ($view) {
            if (Auth::check() && Auth::user()->hasRole('Admin')) {
                $notifications = Auth::user()->unreadNotifications()->latest()->take(5)->get();
                $unreadCount = Auth::user()->unreadNotifications()->count();

                $view->with([
                    'adminNotifications' => $notifications,
                    'adminUnreadCount' => $unreadCount
                ]);
            }
        });

    }
}
