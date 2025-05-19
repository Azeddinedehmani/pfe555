<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Client;
use App\Models\Prescription;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller
{
    protected $invoiceService;
    
    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }
    
    /**
     * Afficher la liste des ventes.
     */
    public function index(Request $request)
    {
        $query = Sale::with(['client', 'user']);
        
        // Filtrage par date
        if ($request->has('date_start') && $request->has('date_end')) {
            $query->whereBetween('created_at', [
                $request->date_start . ' 00:00:00', 
                $request->date_end . ' 23:59:59'
            ]);
        }
        
        // Filtrage par client
        if ($request->has('client_id') && $request->client_id != '') {
            $query->where('client_id', $request->client_id);
        }
        
        // Filtrage par statut de paiement
        if ($request->has('payment_status') && $request->payment_status != '') {
            $query->where('payment_status', $request->payment_status);
        }
        
        // Recherche par référence
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('reference', 'like', "%{$search}%");
        }
        
        $sales = $query->latest()->paginate(15);
        $clients = Client::where('status', 'active')->get();
        
        return view('sales.index', compact('sales', 'clients'));
    }

    /**
     * Afficher l'interface de point de vente.
     */
    public function create()
    {
        $clients = Client::where('status', 'active')->get();
        $products = Product::where('status', 'active')
            ->where('quantity', '>', 0)
            ->get();
        
        return view('sales.create', compact('clients', 'products'));
    }

    /**
     * Enregistrer une nouvelle vente.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'nullable|exists:clients,id',
            'prescription_id' => 'nullable|exists:prescriptions,id',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
            'products.*.discount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'final_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,bank_transfer',
            'payment_status' => 'required|in:paid,unpaid,partial',
            'notes' => 'nullable|string',
        ]);
        
        // Générer la référence unique
        $reference = Sale::generateReference();
        
        DB::beginTransaction();
        
        try {
            // Créer la vente
            $sale = Sale::create([
                'client_id' => $request->client_id,
                'user_id' => Auth::id(),
                'prescription_id' => $request->prescription_id,
                'reference' => $reference,
                'total_amount' => $request->total_amount,
                'discount' => $request->discount ?? 0,
                'tax' => $request->tax ?? 0,
                'final_amount' => $request->final_amount,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'notes' => $request->notes,
            ]);
            
            // Ajouter les produits à la vente
            foreach ($request->products as $product) {
                $saleItem = new SaleItem([
                    'product_id' => $product['id'],
                    'quantity' => $product['quantity'],
                    'price' => $product['price'],
                    'discount' => $product['discount'] ?? 0,
                    'subtotal' => ($product['price'] * $product['quantity']) - ($product['discount'] ?? 0),
                ]);
                
                $sale->items()->save($saleItem);
                
                // Mettre à jour le stock
                $productModel = Product::find($product['id']);
                $productModel->quantity -= $product['quantity'];
                $productModel->save();
            }
            
            // Mettre à jour le statut de l'ordonnance si nécessaire
            if ($request->prescription_id) {
                $prescription = Prescription::find($request->prescription_id);
                $prescription->status = 'completed';
                $prescription->save();
            }
            
            DB::commit();
            
            // Enregistrer l'activité
            activity_log('create', $sale, 'A créé une nouvelle vente: ' . $sale->reference);
            
            return redirect()->route('sales.show', $sale)
                ->with('success', 'Vente effectuée avec succès.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Une erreur est survenue: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Afficher les détails d'une vente.
     */
    public function show(Sale $sale)
    {
        $sale->load(['client', 'user', 'items.product', 'prescription']);
        return view('sales.show', compact('sale'));
    }

    /**
     * Générer une facture pour la vente.
     */
    public function generateInvoice(Sale $sale)
    {
        $pdf = $this->invoiceService->generateInvoice($sale);
        return $pdf->download('facture-' . $sale->reference . '.pdf');
    }
    
    /**
     * Recherche de produits pour l'interface de vente.
     */
    public function searchProducts(Request $request)
    {
        $search = $request->input('q');
        $products = Product::where('status', 'active')
            ->where('quantity', '>', 0)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            })
            ->with('category')
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'text' => $product->name . ' - ' . $product->sell_price . ' DH',
                    'name' => $product->name,
                    'category' => $product->category->name,
                    'price' => $product->sell_price,
                    'stock' => $product->quantity,
                    'barcode' => $product->barcode,
                ];
            });
            
        return response()->json(['results' => $products]);
    }
    
    /**
     * Récupérer les ordonnances d'un client.
     */
    public function getClientPrescriptions(Request $request)
    {
        $clientId = $request->input('client_id');
        $prescriptions = Prescription::where('client_id', $clientId)
            ->where('status', 'active')
            ->get()
            ->map(function ($prescription) {
                return [
                    'id' => $prescription->id,
                    'text' => 'Dr. ' . $prescription->doctor_name . ' - ' . $prescription->getFormattedDate(),
                ];
            });
            
        return response()->json(['results' => $prescriptions]);
    }
}