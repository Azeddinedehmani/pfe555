<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Sale;
use App\Models\Client;
use App\Models\Supplier;
use App\Models\Prescription;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Affiche le tableau de bord.
     */
    public function index()
    {
        // Statistiques des produits
        $totalProducts = Product::count();
        $lowStockProducts = Product::lowStock()->count();
        $expiringProducts = Product::expiringSoon()->count();
        $expiredProducts = Product::expired()->count();
        
        // Statistiques des ventes
        $todaySales = Sale::today()->count();
        $todayRevenue = Sale::today()->sum('final_amount');
        $monthSales = Sale::thisMonth()->count();
        $monthRevenue = Sale::thisMonth()->sum('final_amount');
        
        // Statistiques des clients et fournisseurs
        $totalClients = Client::count();
        $totalSuppliers = Supplier::count();
        
        // Ordonnances
        $expiringPrescriptions = Prescription::expiringSoon()->count();
        
        // Dernières activités
        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->take(10)
            ->get();
        
        // Dernières ventes
        $recentSales = Sale::with(['client', 'user'])
            ->latest()
            ->take(5)
            ->get();
        
        // Produits à faible stock
        $criticalProducts = Product::lowStock()
            ->with('category')
            ->take(5)
            ->get();
        
        return view('dashboard.index', compact(
            'totalProducts',
            'lowStockProducts',
            'expiringProducts',
            'expiredProducts',
            'todaySales',
            'todayRevenue',
            'monthSales',
            'monthRevenue',
            'totalClients',
            'totalSuppliers',
            'expiringPrescriptions',
            'recentActivities',
            'recentSales',
            'criticalProducts'
        ));
    }
}