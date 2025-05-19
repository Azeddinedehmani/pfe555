<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Prescription;
use App\Models\Client;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Afficher le rapport des ventes.
     */
    public function salesReport(Request $request)
    {
        // Définir la période par défaut (mois en cours)
        $start_date = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $end_date = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        // Construire la requête de base
        $query = Sale::whereBetween('created_at', [
            $start_date . ' 00:00:00',
            $end_date . ' 23:59:59'
        ]);
        
        // Filtrer par statut de paiement
        if ($request->has('payment_status') && $request->payment_status != '') {
            $query->where('payment_status', $request->payment_status);
        }
        
        // Filtrer par méthode de paiement
        if ($request->has('payment_method') && $request->payment_method != '') {
            $query->where('payment_method', $request->payment_method);
        }
        
        // Filtrer par client
        if ($request->has('client_id') && $request->client_id != '') {
            $query->where('client_id', $request->client_id);
        }
        
        // Récupérer les ventes
        $sales = $query->with(['client', 'user'])->latest()->get();
        
        // Calculer les statistiques
        $totalSales = $sales->count();
        $totalRevenue = $sales->sum('final_amount');
        $averageRevenue = $totalSales > 0 ? $totalRevenue / $totalSales : 0;
        
        // Calculer les statistiques par jour
        $salesByDay = $sales->groupBy(function($sale) {
            return Carbon::parse($sale->created_at)->format('Y-m-d');
        })->map(function($salesGroup) {
            return [
                'count' => $salesGroup->count(),
                'revenue' => $salesGroup->sum('final_amount')
            ];
        });
        
        // Calculer les statistiques par méthode de paiement
        $salesByPaymentMethod = $sales->groupBy('payment_method')->map(function($salesGroup) {
            return [
                'count' => $salesGroup->count(),
                'revenue' => $salesGroup->sum('final_amount')
            ];
        });
        
        // Calculer les produits les plus vendus
        $topProducts = $sales->flatMap(function($sale) {
            return $sale->items;
        })->groupBy('product_id')->map(function($items) {
            $product = Product::find($items->first()->product_id);
            return [
                'product' => $product,
                'quantity' => $items->sum('quantity'),
                'revenue' => $items->sum('subtotal')
            ];
        })->sortByDesc('quantity')->take(10);
        
        // Liste des clients pour le filtre
        $clients = Client::where('status', 'active')->get();
        
        return view('reports.sales', compact(
            'sales',
            'start_date',
            'end_date',
            'totalSales',
            'totalRevenue',
            'averageRevenue',
            'salesByDay',
            'salesByPaymentMethod',
            'topProducts',
            'clients'
        ));
    }
    
    /**
     * Afficher le rapport d'inventaire.
     */
    public function inventoryReport(Request $request)
    {
        // Construire la requête de base
        $query = Product::with('category');
        
        // Filtrer par catégorie
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }
        
        // Filtrer par statut de stock
        if ($request->has('stock_status')) {
            if ($request->stock_status == 'low') {
                $query->lowStock();
            } elseif ($request->stock_status == 'out') {
                $query->where('quantity', 0);
            }
        }
        
        // Filtrer par statut d'expiration
        if ($request->has('expiry_status')) {
            if ($request->expiry_status == 'expired') {
                $query->expired();
            } elseif ($request->expiry_status == 'expiring_soon') {
                $query->expiringSoon();
            }
        }
        
        // Récupérer les produits
        $products = $query->get();
        
        // Calculer les statistiques
        $totalProducts = $products->count();
        $totalValue = $products->sum(function($product) {
            return $product->quantity * $product->buy_price;
        });
        $totalSellValue = $products->sum(function($product) {
            return $product->quantity * $product->sell_price;
        });
        $potentialProfit = $totalSellValue - $totalValue;
        
        // Statistiques par catégorie
        $statsByCategory = $products->groupBy('category.name')->map(function($productsGroup) {
            return [
                'count' => $productsGroup->count(),
                'value' => $productsGroup->sum(function($product) {
                    return $product->quantity * $product->buy_price;
                }),
                'sell_value' => $productsGroup->sum(function($product) {
                    return $product->quantity * $product->sell_price;
                })
            ];
        });
        
        // Statistiques d'alerte
        $lowStockCount = $products->filter(function($product) {
            return $product->isLowStock();
        })->count();
        
        $outOfStockCount = $products->filter(function($product) {
            return $product->quantity <= 0;
        })->count();
        
        $expiredCount = $products->filter(function($product) {
            return $product->isExpired();
        })->count();
        
        $expiringSoonCount = $products->filter(function($product) {
            return $product->isExpiringSoon() && !$product->isExpired();
        })->count();
        
        // Obtenir les catégories pour le filtre
        $categories = \App\Models\Category::all();
        
        return view('reports.inventory', compact(
            'products',
            'totalProducts',
            'totalValue',
            'totalSellValue',
            'potentialProfit',
            'statsByCategory',
            'lowStockCount',
            'outOfStockCount',
            'expiredCount',
            'expiringSoonCount',
            'categories'
        ));
    }
    
    /**
     * Afficher le rapport des clients.
     */
    public function clientsReport(Request $request)
    {
        // Période par défaut (année en cours)
        $start_date = $request->input('start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
        $end_date = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        // Récupérer tous les clients
        $clients = Client::where('status', 'active')->get();
        
        // Pour chaque client, calculer les statistiques
        $clientsData = $clients->map(function($client) use ($start_date, $end_date) {
            $sales = $client->sales()
                ->whereBetween('created_at', [
                    $start_date . ' 00:00:00',
                    $end_date . ' 23:59:59'
                ])
                ->get();
            
            return [
                'client' => $client,
                'sales_count' => $sales->count(),
                'total_spent' => $sales->sum('final_amount'),
                'first_purchase' => $client->sales()->orderBy('created_at')->first()?->created_at,
                'last_purchase' => $client->sales()->orderBy('created_at', 'desc')->first()?->created_at,
                'prescriptions_count' => $client->prescriptions()->count()
            ];
        });
        
        // Trier par dépense totale
        $clientsData = $clientsData->sortByDesc('total_spent');
        
        // Calculer les statistiques globales
        $totalClients = $clients->count();
        $activePeriodClients = $clientsData->filter(function($data) {
            return $data['sales_count'] > 0;
        })->count();
        
        $totalRevenue = $clientsData->sum('total_spent');
        $averageRevenuePerClient = $activePeriodClients > 0 ? $totalRevenue / $activePeriodClients : 0;
        
        return view('reports.clients', compact(
            'clientsData',
            'start_date',
            'end_date',
            'totalClients',
            'activePeriodClients',
            'totalRevenue',
            'averageRevenuePerClient'
        ));
    }
}