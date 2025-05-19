<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Afficher la liste des produits.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');
        
        // Filtrage par catégorie
        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }
        
        // Filtrage par statut
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Filtrage par stock
        if ($request->has('stock_status')) {
            if ($request->stock_status == 'low') {
                $query->lowStock();
            } elseif ($request->stock_status == 'out') {
                $query->where('quantity', 0);
            }
        }
        
        // Recherche par nom ou code-barres
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }
        
        // Filtrage par date d'expiration
        if ($request->has('expiry_status')) {
            if ($request->expiry_status == 'expired') {
                $query->expired();
            } elseif ($request->expiry_status == 'expiring_soon') {
                $query->expiringSoon();
            }
        }
        
        $products = $query->latest()->paginate(15);
        $categories = Category::where('status', 'active')->get();
        
        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Afficher le formulaire de création.
     */
    public function create()
    {
        $categories = Category::where('status', 'active')->get();
        return view('products.create', compact('categories'));
    }

    /**
     * Enregistrer un nouveau produit.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'barcode' => 'nullable|string|unique:products,barcode',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'alert_quantity' => 'required|integer|min:0',
            'expiry_date' => 'nullable|date',
            'manufacturing_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);
        
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }
        
        $product = Product::create($validated);
        
        // Enregistrement de l'activité
        activity_log('create', $product, 'A créé un nouveau produit: ' . $product->name);
        
        return redirect()->route('products.index')
            ->with('success', 'Produit créé avec succès.');
    }

    /**
     * Afficher les détails d'un produit.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Afficher le formulaire de modification.
     */
    public function edit(Product $product)
    {
        $categories = Category::where('status', 'active')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Mettre à jour un produit.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'barcode' => [
                'nullable',
                'string',
                Rule::unique('products')->ignore($product->id),
            ],
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'alert_quantity' => 'required|integer|min:0',
            'expiry_date' => 'nullable|date',
            'manufacturing_date' => 'nullable|date',
            'location' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);
        
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }
        
        $product->update($validated);
        
        // Enregistrement de l'activité
        activity_log('update', $product, 'A mis à jour le produit: ' . $product->name);
        
        return redirect()->route('products.index')
            ->with('success', 'Produit mis à jour avec succès.');
    }

    /**
     * Supprimer un produit.
     */
    public function destroy(Product $product)
    {
        $productName = $product->name;
        
        // Supprimer l'image si elle existe
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();
        
        // Enregistrement de l'activité
        activity_log('delete', $product, 'A supprimé le produit: ' . $productName);
        
        return redirect()->route('products.index')
            ->with('success', 'Produit supprimé avec succès.');
    }
    
    /**
     * Liste des produits à faible stock
     */
    public function lowStock()
    {
        $products = Product::lowStock()->with('category')->latest()->paginate(15);
        return view('products.low_stock', compact('products'));
    }
    
    /**
     * Liste des produits expirés ou expirant bientôt
     */
    public function expiringSoon()
    {
        $expiredProducts = Product::expired()->with('category')->get();
        $expiringSoonProducts = Product::expiringSoon()->with('category')->get();
        
        return view('products.expiring', compact('expiredProducts', 'expiringSoonProducts'));
    }
}