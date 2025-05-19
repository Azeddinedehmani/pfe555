<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Routes d'authentification
require __DIR__.'/auth.php';

// Routes pour les utilisateurs authentifiés
Route::middleware(['auth'])->group(function () {
    // Tableau de bord
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Gestion des produits
    Route::resource('products', ProductController::class);
    Route::get('/products-low-stock', [ProductController::class, 'lowStock'])->name('products.low.stock');
    Route::get('/products-expiring', [ProductController::class, 'expiringSoon'])->name('products.expiring');
    
    // Gestion des catégories
    Route::resource('categories', CategoryController::class);
    
    // Gestion des ventes
    Route::resource('sales', SaleController::class);
    Route::get('/sales/{sale}/invoice', [SaleController::class, 'generateInvoice'])->name('sales.invoice');
    Route::get('/sales-search-products', [SaleController::class, 'searchProducts'])->name('sales.search.products');
    Route::get('/sales-get-client-prescriptions', [SaleController::class, 'getClientPrescriptions'])->name('sales.client.prescriptions');
    
    // Gestion des achats
    Route::resource('purchases', PurchaseController::class);
    
    // Gestion des clients
    Route::resource('clients', ClientController::class);
    
    // Gestion des fournisseurs
    Route::resource('suppliers', SupplierController::class);
    
    // Gestion des ordonnances
    Route::resource('prescriptions', PrescriptionController::class);
    
    // Rapports
    Route::get('/reports/sales', [ReportController::class, 'salesReport'])->name('reports.sales');
    Route::get('/reports/inventory', [ReportController::class, 'inventoryReport'])->name('reports.inventory');
    Route::get('/reports/clients', [ReportController::class, 'clientsReport'])->name('reports.clients');
    
    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update.password');
});

// Routes pour les administrateurs/responsables
Route::middleware(['auth', 'role:responsable'])->group(function () {
    // Gestion des utilisateurs
    Route::resource('users', UserController::class);
    
    // Paramètres du système
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
    
    // Journaux d'activité
    Route::get('/activity-logs', [UserController::class, 'activityLogs'])->name('activity.logs');
    
    // Sauvegarde et restauration
    Route::get('/backup', [SettingController::class, 'backup'])->name('backup');
    Route::post('/restore', [SettingController::class, 'restore'])->name('restore');
});