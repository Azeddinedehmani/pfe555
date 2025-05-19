@extends('layouts.app')

@section('title', 'Gestion des produits')

@section('header', 'Gestion des produits')

@section('content')
<div class="mb-4 flex flex-col md:flex-row justify-between items-start md:items-center">
    <div class="mb-4 md:mb-0">
        <a href="{{ route('products.create') }}" class="btn-primary">
            <i class="fas fa-plus mr-1"></i> Ajouter un produit
        </a>
    </div>
    
    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
        <a href="{{ route('products.low.stock') }}" class="btn-secondary">
            <i class="fas fa-exclamation-triangle mr-1"></i> Stock faible
        </a>
        <a href="{{ route('products.expiring') }}" class="btn-warning">
            <i class="fas fa-calendar-times mr-1"></i> Expirations
        </a>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg shadow">
    <!-- Filtres -->
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h2 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">Filtres</h2>
        
        <form action="{{ route('products.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Recherche
                </label>
                <input 
                    type="text" 
                    id="search" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Nom ou code-barres"
                    class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                >
            </div>
            
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Catégorie
                </label>
                <select 
                    id="category" 
                    name="category" 
                    class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                >
                    <option value="">Toutes les catégories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="stock_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Stock
                </label>
                <select 
                    id="stock_status" 
                    name="stock_status" 
                    class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                >
                    <option value="">Tous</option>
                    <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Stock faible</option>
                    <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Rupture de stock</option>
                </select>
            </div>
            
            <div>
                <label for="expiry_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Expiration
                </label>
                <select 
                    id="expiry_status" 
                    name="expiry_status" 
                    class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                >
                    <option value="">Tous</option>
                    <option value="expired" {{ request('expiry_status') == 'expired' ? 'selected' : '' }}>Expirés</option>
                    <option value="expiring_soon" {{ request('expiry_status') == 'expiring_soon' ? 'selected' : '' }}>Expirant bientôt</option>
                </select>
            </div>
            
            <div class="md:col-span-4 flex justify-end">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-search mr-1"></i> Filtrer
                </button>
                <a href="{{ route('products.index') }}" class="btn-secondary ml-2">
                    <i class="fas fa-times mr-1"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>
    
    <!-- Liste des produits -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Produit
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Catégorie
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Prix
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Stock
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Expiration
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Statut
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($products as $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                @if($product->image)
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded object-cover" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                    </div>
                                @else
                                    <div class="flex-shrink-0 h-10 w-10 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                                        <i class="fas fa-pills text-gray-500 dark:text-gray-400"></i>
                                    </div>
                                @endif
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $product->name }}
                                    </div>
                                    @if($product->barcode)
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-barcode mr-1"></i> {{ $product->barcode }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ $product->category->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ number_format($product->sell_price, 2) }} DH</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Achat: {{ number_format($product->buy_price, 2) }} DH</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($product->isLowStock())
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                    {{ $product->quantity }} <small class="ml-1">({{ $product->alert_quantity }})</small>
                                </span>
                            @elseif($product->quantity <= 0)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                    Épuisé
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    {{ $product->quantity }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($product->expiry_date)
                                @if($product->isExpired())
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                        Expiré le {{ $product->expiry_date->format('d/m/Y') }}
                                    </span>
                                @elseif($product->isExpiringSoon())
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                                        {{ $product->expiry_date->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $product->expiry_date->format('d/m/Y') }}
                                    </span>
                                @endif
                            @else
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    N/A
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($product->status == 'active')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    Actif
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    Inactif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('products.show', $product) }}" class="text-primary dark:text-primary-dark hover:text-primary-dark dark:hover:text-primary-light mr-3">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('products.edit', $product) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                            Aucun produit trouvé.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
        {{ $products->withQueryString()->links() }}
    </div>
</div>
@endsection