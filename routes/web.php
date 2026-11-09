<?php

use App\Http\Controllers\BusController;
use App\Http\Controllers\Customer;
use App\Http\Controllers\Portal;
use App\Http\Controllers\TripController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', [TripController::class, 'home'])->name('home');

Route::get('/bus', [BusController::class, 'display']);

Route::get('/routes', [TripController::class, 'routes'])->name('routes.list');
Route::get('/trips', [TripController::class, 'index'])->name('trips.index');
Route::get('/trips/{schedule}', [TripController::class, 'show'])->name('trips.show');

/*
|--------------------------------------------------------------------------
| Logged in
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Everyone lands here after login and is sent to their own portal
    Route::get('/dashboard', function (Request $request) {
        return $request->user()->isStaff()
            ? redirect()->route('portal.dashboard')
            : redirect()->route('customer.dashboard');
    })->name('dashboard');

    // Customer portal
    Route::middleware('role:customer')->name('customer.')->group(function () {
        Route::get('/my/dashboard', Customer\DashboardController::class)->name('dashboard');
        Route::post('/trips/{schedule}/reserve', [Customer\ReservationController::class, 'store'])->name('reservations.store');
        Route::get('/my/reservations', [Customer\ReservationController::class, 'index'])->name('reservations.index');
        Route::get('/my/reservations/{reservation}', [Customer\ReservationController::class, 'show'])->name('reservations.show');
        Route::patch('/my/reservations/{reservation}/cancel', [Customer\ReservationController::class, 'cancel'])->name('reservations.cancel');
    });

    // Staff portal (admin + employee)
    Route::middleware('role:admin,employee')->prefix('portal')->name('portal.')->group(function () {
        Route::get('/', Portal\DashboardController::class)->name('dashboard');

        Route::resource('buses', Portal\BusController::class);
        Route::resource('routes', Portal\BusRouteController::class)->parameters(['routes' => 'busRoute']);
        Route::resource('schedules', Portal\ScheduleController::class);

        Route::get('reservations', [Portal\ReservationController::class, 'index'])->name('reservations.index');
        Route::get('reservations/{reservation}', [Portal\ReservationController::class, 'show'])->name('reservations.show');
        Route::patch('reservations/{reservation}/approve', [Portal\ReservationController::class, 'approve'])->name('reservations.approve');
        Route::patch('reservations/{reservation}/reject', [Portal\ReservationController::class, 'reject'])->name('reservations.reject');
        Route::patch('reservations/{reservation}/cancel', [Portal\ReservationController::class, 'cancel'])->name('reservations.cancel');

        // Admin only
        Route::middleware('role:admin')->group(function () {
            Route::resource('users', Portal\UserController::class)->except('show');
            Route::get('activity-logs', Portal\ActivityLogController::class)->name('activity-logs.index');
        });
    });
});
