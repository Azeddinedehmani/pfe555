@extends('layouts.app')

@section('title', isset($product) ? 'Modifier un produit' : 'Ajouter un produit')

@section('header', isset($product) ? 'Modifier un produit' : 'Ajouter un produit')

@section('content')
<div class="mb-4">
    <a href="{{ route('products.index') }}" class="btn-secondary">
        <i class="fas fa-arrow-left mr-1"></i> Retour à la liste
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg shadow">
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h2 class="font-semibold text-gray-800 dark:text-gray-200">
            {{ isset($product) ? 'Modifier le produit: ' . $product->name : 'Ajouter un nouveau produit' }}
        </h2>
    </div>
    
    <form action="{{ isset($product) ? route('products.update', $product) : route('products.store') }}" method="POST" enctype="multipart/form-data" class="p-4">
        @csrf
        @if(isset($product))
            @method('PUT')
        @endif
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <!-- Informations de base -->
            <div>
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nom du produit <span class="text-red-600">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', isset($product) ? $product->name : '') }}" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                        required
                    >
                    @error('name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Catégorie <span class="text-red-600">*</span>
                    </label>
                    <select 
                        id="category_id" 
                        name="category_id" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                        required
                    >
                        <option value="">Sélectionner une catégorie</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', isset($product) ? $product->category_id : '') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="barcode" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Code-barres
                    </label>
                    <input 
                        type="text" 
                        id="barcode" 
                        name="barcode" 
                        value="{{ old('barcode', isset($product) ? $product->barcode : '') }}" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                    >
                    @error('barcode')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Description
                    </label>
                    <textarea 
                        id="description" 
                        name="description" 
                        rows="4" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                    >{{ old('description', isset($product) ? $product->description : '') }}</textarea>
                    @error('description')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Emplacement
                    </label>
                    <input 
                        type="text" 
                        id="location" 
                        name="location" 
                        value="{{ old('location', isset($product) ? $product->location : '') }}" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                    >
                    @error('location')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Prix, stock et dates -->
            <div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="buy_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Prix d'achat <span class="text-red-600">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="buy_price" 
                            name="buy_price" 
                            value="{{ old('buy_price', isset($product) ? $product->buy_price : '') }}" 
                            step="0.01" 
                            min="0" 
                            class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                            required
                        >
                        @error('buy_price')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="sell_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Prix de vente <span class="text-red-600">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="sell_price" 
                            name="sell_price" 
                            value="{{ old('sell_price', isset($product) ? $product->sell_price : '') }}" 
                            step="0.01" 
                            min="0" 
                            class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                            required
                        >
                        @error('sell_price')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Quantité en stock <span class="text-red-600">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="quantity" 
                            name="quantity" 
                            value="{{ old('quantity', isset($product) ? $product->quantity : '0') }}" 
                            min="0" 
                            class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                            required
                        >
                        @error('quantity')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="alert_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Alerte stock <span class="text-red-600">*</span>
                        </label>
                        <input 
                            type="number" 
                            id="alert_quantity" 
                            name="alert_quantity" 
                            value="{{ old('alert_quantity', isset($product) ? $product->alert_quantity : '10') }}" 
                            min="0" 
                            class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                            required
                        >
                        @error('alert_quantity')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="manufacturing_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Date de fabrication
                        </label>
                        <input 
                            type="date" 
                            id="manufacturing_date" 
                            name="manufacturing_date" 
                            value="{{ old('manufacturing_date', isset($product) && $product->manufacturing_date ? $product->manufacturing_date->format('Y-m-d') : '') }}" 
                            class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                        >
                        @error('manufacturing_date')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="expiry_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Date d'expiration
                        </label>
                        <input 
                            type="date" 
                            id="expiry_date" 
                            name="expiry_date" 
                            value="{{ old('expiry_date', isset($product) && $product->expiry_date ? $product->expiry_date->format('Y-m-d') : '') }}" 
                            class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                        >
                        @error('expiry_date')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Statut <span class="text-red-600">*</span>
                    </label>
                    <select 
                        id="status" 
                        name="status" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                        required
                    >
                        <option value="active" {{ old('status', isset($product) ? $product->status : '') == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ old('status', isset($product) ? $product->status : '') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                    </select>
                    @error('status')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Image du produit
                    </label>
                    <input 
                        type="file" 
                        id="image" 
                        name="image" 
                        accept="image/*" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                    >
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Formats acceptés: JPG, PNG, GIF. Taille max: 2MB.
                    </p>
                    @error('image')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    
                    @if(isset($product) && $product->image)
                        <div class="mt-2">
                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-1">Image actuelle:</p>
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-32 w-auto object-cover rounded">
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 flex justify-end">
            <a href="{{ route('products.index') }}" class="btn-secondary mr-2">
                Annuler
            </a>
            <button type="submit" class="btn-primary">
                <i class="fas fa-save mr-1"></i> {{ isset($product) ? 'Mettre à jour' : 'Enregistrer' }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Calcul automatique du prix de vente basé sur le prix d'achat (avec marge par défaut de 30%)
    document.addEventListener('DOMContentLoaded', function() {
        const buyPriceInput = document.getElementById('buy_price');
        const sellPriceInput = document.getElementById('sell_price');
        
        buyPriceInput.addEventListener('input', function() {
            if (sellPriceInput.value === '' || parseFloat(sellPriceInput.value) === 0) {
                const buyPrice = parseFloat(this.value) || 0;
                const margin = 1.3; // 30% de marge
                sellPriceInput.value = (buyPrice * margin).toFixed(2);
            }
        });
    });
</script>
@endpush