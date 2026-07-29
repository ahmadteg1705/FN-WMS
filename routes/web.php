<?php

use App\Http\Controllers\MarketingController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\RouterController;
use App\Http\Controllers\OdpController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\WorkOrderAccountController;
use App\Http\Controllers\WorkOrderInstallationController;
use App\Http\Controllers\NocActivationController;

Route::get('/registrations/{registration}/status', [RegistrationController::class, 'editStatus'])
    ->name('registrations.status.edit');
Route::put('/registrations/{registration}/status', [RegistrationController::class, 'updateStatus'])
    ->name('registrations.status.update');
Route::get('/registrations/odp/{odp}/info', [RegistrationController::class, 'odpInfo'])
    ->name('registrations.odp.info');
Route::get('/reports/registrations/excel', [ReportController::class, 'registrationExcel'])
    ->name('reports.registrations.excel');

Route::get('/odps/export', [OdpController::class, 'export'])
    ->name('odps.export');
    
Route::get('/odps/template', [OdpController::class, 'downloadTemplate'])
    ->name('odps.template');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::middleware('permission:noc-activations.view')
    ->prefix('activation')
    ->group(function () {
        Route::get('/', [NocActivationController::class, 'index'])
            ->name('noc-activations.index');

        Route::post('/{nocActivation}/accept', [NocActivationController::class, 'accept'])
            ->middleware('permission:noc-activations.accept')
            ->name('noc-activations.accept');

        Route::get('/{nocActivation}/process', [NocActivationController::class, 'process'])
            ->middleware('permission:noc-activations.process')
            ->name('noc-activations.process');

        Route::post('/{nocActivation}/complete', [NocActivationController::class, 'complete'])
            ->middleware('permission:noc-activations.process')
            ->name('noc-activations.complete');
    });
    Route::middleware('permission:roles.view')->group(function () {
        Route::get('roles/{role}/permissions', [RoleController::class, 'permissions'])
            ->name('roles.permissions');

        Route::put('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])
            ->name('roles.permissions.update');

        Route::resource('roles', RoleController::class);
    });

    Route::post('/registrations/{registration}/verify', [RegistrationController::class, 'verify'])
        ->name('registrations.verify');

    /*
    |--------------------------------------------------------------------------
    | Modul Aktivasi NOC
    |--------------------------------------------------------------------------
    */
    Route::prefix('activation')
        ->name('noc-activations.')
        ->middleware('permission:noc-activations.view')
        ->group(function () {
            Route::get('/', [NocActivationController::class, 'index'])
                ->name('index');

            Route::post('/{nocActivation}/accept', [NocActivationController::class, 'accept'])
                ->middleware('permission:noc-activations.accept')
                ->name('accept');
        });
    Route::middleware('permission:users.view')->group(function () {

    Route::resource('users', UserController::class)->except('show');

});

Route::middleware('permission:work-orders.view')->group(function () {

        Route::get(
            '/teams/{team}/members',
            [WorkOrderController::class, 'getTeamMembers']
        )->name('teams.members');

        Route::resource('work-orders', WorkOrderController::class);
        Route::resource('work-order-accounts', WorkOrderAccountController::class)->only
        ([
            'store',
            'update',
        ]);
        Route::post('/work-orders/{workOrder}/accept', [WorkOrderController::class, 'accept'])
    ->name('work-orders.accept');
    Route::post('/work-orders/{workOrder}/preparation',
    [WorkOrderController::class, 'preparation'])
    ->name('work-orders.preparation');
    Route::post(
    '/work-orders/{workOrder}/depart',
    [WorkOrderController::class, 'depart']
    )->name('work-orders.depart');
    Route::post(
        '/work-orders/{workOrder}/arrive',
        [WorkOrderController::class, 'arrive']
    )->name('work-orders.arrive');
    Route::post(
        '/work-orders/{workOrder}/installation',
        [WorkOrderController::class, 'installation']
    )->name('work-orders.installation');
    Route::post(
        '/work-orders/{workOrder}/waiting-verification',
        [WorkOrderController::class, 'waitingVerification']
    )->name('work-orders.waiting-verification');
    Route::post(
        '/work-orders/{workOrder}/complete',
        [WorkOrderController::class, 'complete']
    )->name('work-orders.complete');
    Route::post(
        '/work-orders/{workOrder}/customer-not-found',
        [WorkOrderController::class, 'customerNotFound']
    )->name('work-orders.customer-not-found');
    Route::post(
    '/work-orders/{workOrder}/reschedule-request',
    [WorkOrderController::class, 'rescheduleRequest']
)->name('work-orders.reschedule-request');
Route::get(
    '/work-orders/{workOrder}/reschedule',
    [WorkOrderController::class, 'reschedule']
)->name('work-orders.reschedule');
Route::post(
    '/work-orders/{workOrder}/reschedule',
    [WorkOrderController::class, 'updateSchedule']
)->name('work-orders.reschedule.update');
Route::post(
    '/work-orders/{workOrder}/installation/start',
    [WorkOrderController::class, 'installation']
)->name('work-orders.installation');
Route::get(
    '/work-orders/{workOrder}/installation',
    [WorkOrderInstallationController::class, 'edit']
)->name('work-order-installation.edit');
Route::post(
    '/work-orders/{workOrder}/installation/save',
    [WorkOrderInstallationController::class, 'update']
)->name('work-order-installation.update');
Route::post(
    '/work-orders/{workOrder}/installation/complete',
    [WorkOrderInstallationController::class, 'completeInstallation']
)->name('work-order-installation.complete');
    });
    
    Route::resource('marketings', MarketingController::class);
    Route::resource('registrations', RegistrationController::class);
    Route::resource('teams', TeamController::class);
    Route::post('/technicians/import', [TechnicianController::class, 'import'])
    ->name('technicians.import');
    Route::get('/technicians/template', [TechnicianController::class, 'downloadTemplate'])
    ->name('technicians.template');
    Route::get('/technicians/export', [TechnicianController::class, 'export'])
    ->name('technicians.export');
    Route::resource('positions', PositionController::class);
    Route::resource('technicians', TechnicianController::class);
    Route::resource('routers', RouterController::class);
    Route::resource('odps', OdpController::class);
    Route::post('/packages/import', [PackageController::class, 'import'])
    ->name('packages.import');
    Route::resource('packages', PackageController::class);
    Route::resource('customers', CustomerController::class);
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/odps/import', [OdpController::class, 'import'])
    ->name('odps.import');
    Route::get('/odps/template', [OdpController::class, 'downloadTemplate'])
        ->name('odps.template');
});

require __DIR__.'/auth.php';
