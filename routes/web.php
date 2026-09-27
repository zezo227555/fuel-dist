<?php

use App\Http\Controllers\Auth\CitizenRegisteredUserController;
use App\Http\Controllers\Citizen\StationController as CitizenStationController;
use App\Http\Controllers\Citizen\VehicleController;
use App\Http\Controllers\CitizenDocumentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Staff\CooldownController;
use App\Http\Controllers\Staff\DashboardController;
use App\Http\Controllers\Staff\ReportController;
use App\Http\Controllers\Staff\StationController as StaffStationController;
use App\Http\Controllers\Staff\VehicleReviewController;
use App\Http\Controllers\Station\DeskController;
use App\Http\Controllers\VehicleImageController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::middleware('guest')->group(function () {
    Route::get('register', [CitizenRegisteredUserController::class, 'create'])
        ->name('register');
    Route::post('register', [CitizenRegisteredUserController::class, 'store']);
});

Route::middleware(['auth'])->group(function () {
    Route::get('/home', function () {
        /** @var User $user */
        $user = auth()->user();

        return view('citizen.home', [
            'user' => $user,
            'vehicles' => $user->vehicles()->latest()->take(5)->get(),
        ]);
    })->name('citizen.home');

    Route::get('/dashboard', DashboardController::class)
        ->middleware('permission:access.staff-panel')
        ->name('dashboard');

    Route::middleware('permission:access.station-desk')->prefix('station')->name('station.')->group(function () {
        Route::get('/', [DeskController::class, 'show'])->name('desk');
        Route::post('/lookup', [DeskController::class, 'lookup'])->name('lookup');
        Route::post('/fill', [DeskController::class, 'confirm'])->name('fill');
        Route::redirect('/demo', '/station')->name('demo');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/identity', [ProfileController::class, 'updateIdentity'])->name('profile.identity.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/documents/{document}', [CitizenDocumentController::class, 'show'])
        ->name('citizen.documents.show');

    Route::middleware('role:citizen')->group(function () {
        Route::get('/stations', [CitizenStationController::class, 'index'])->name('citizen.stations.index');
        Route::get('/stations/{station}', [CitizenStationController::class, 'show'])->name('citizen.stations.show');

        Route::get('/vehicles', [VehicleController::class, 'index'])->name('citizen.vehicles.index');
        Route::get('/vehicles/create', [VehicleController::class, 'create'])->name('citizen.vehicles.create');
        Route::post('/vehicles', [VehicleController::class, 'store'])->name('citizen.vehicles.store');
        Route::get('/vehicles/{vehicle}', [VehicleController::class, 'show'])->name('citizen.vehicles.show');
    });

    Route::get('/vehicle-images/{image}', [VehicleImageController::class, 'show'])
        ->name('vehicle.images.show');

    Route::middleware('permission:vehicles.review')->prefix('staff')->name('staff.')->group(function () {
        Route::get('/vehicles', [VehicleReviewController::class, 'index'])->name('vehicles.index');
        Route::get('/vehicles/{vehicle}', [VehicleReviewController::class, 'show'])->name('vehicles.show');
        Route::post('/vehicles/{vehicle}/review', [VehicleReviewController::class, 'store'])->name('vehicles.review');
    });

    Route::middleware('permission:stations.manage')->prefix('staff')->name('staff.')->group(function () {
        Route::resource('stations', StaffStationController::class);
    });

    Route::middleware('permission:access.audit-reports')->prefix('staff')->name('staff.')->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/fill-ups', [ReportController::class, 'fillUps'])->name('reports.fill-ups');
        Route::get('/reports/vehicles', [ReportController::class, 'vehicleHistory'])->name('reports.vehicle-history');
        Route::get('/reports/denials', [ReportController::class, 'denials'])->name('reports.denials');
        Route::get('/reports/reviews', [ReportController::class, 'reviews'])->name('reports.reviews');
        Route::get('/reports/cooldown', [ReportController::class, 'cooldown'])->name('reports.cooldown');
    });

    Route::middleware('permission:cooldown.manage|access.audit-reports')->prefix('staff')->name('staff.')->group(function () {
        Route::get('/cooldown', [CooldownController::class, 'show'])->name('cooldown.show');
    });

    Route::middleware('permission:cooldown.manage')->prefix('staff')->name('staff.')->group(function () {
        Route::post('/cooldown', [CooldownController::class, 'store'])->name('cooldown.store');
        Route::post('/cooldown/{changeRequest}/approve', [CooldownController::class, 'approve'])->name('cooldown.approve');
        Route::post('/cooldown/{changeRequest}/reject', [CooldownController::class, 'reject'])->name('cooldown.reject');
    });
});

require __DIR__.'/auth.php';
