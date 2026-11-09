<?php

namespace App\Providers;

use App\Models\Reservation;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // Pending-approval count for the "Reservations" badge in the staff sidebar
        View::composer('layouts.portal', function ($view) {
            $view->with('pendingCount', Reservation::where('status', 'pending')->count());
        });
    }
}
