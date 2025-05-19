@extends('layouts.app')

@section('title', 'Nouvelle vente')

@section('header', 'Nouvelle vente')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container {
        width: 100% !important;
    }
    .select2-container--default .select2-selection--single {
        border-radius: 0.375rem;
        height: 42px;
        border-color: #e2e8f0;
        padding: 0.5rem 0.75rem;
    }
    .dark .select2-container--default .select2-selection--single {
        background-color: #1f2937;
        border-color: #374151;
        color: #e5e7eb;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 24px;
        color: inherit;
    }
    .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #e5e7eb;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
    .select2-dropdown {
        border-color: #e2e8f0;
    }
    .dark .select2-dropdown {
        background-color: #1f2937;
        border-color: #374151;
    }
    .dark .select2-search__field {
        background-color: #374151;
        color: #e5e7eb;
    }
    .dark .select2-results__option {
        color: #e5e7eb;
    }
    .dark .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #4a90e2;
    }
</style>
@endpush

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-lg shadow">
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h2 class="font-semibold text-gray-800 dark:text-gray-200">Nouvelle vente</h2>
    </div>
    
    <form id="sale-form" method="POST" action="{{ route('sales.store') }}" class="p-4">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- Informations client -->
            <div class="md:col-span-2">
                <div class="mb-4">
                    <label for="client_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Client
                    </label>
                    <select id="client_id" name="client_id" class="client-select w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50">
                        <option value="">Client anonyme</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }} - {{ $client->phone }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="prescription_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Ordonnance
                    </label>
                    <select id="prescription_id" name="prescription_id" class="prescription-select w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50" disabled>
                        <option value="">Sélectionner une ordonnance</option>
                    </select>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Sélectionnez un client pour voir ses ordonnances.
                    </p>
                </div>
            </div>
            
            <!-- Informations vente -->
            <div>
                <div class="mb-4">
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Mode de paiement
                    </label>
                    <select id="payment_method" name="payment_method" class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50">
                        <option value="cash">Espèces</option>
                        <option value="card">Carte bancaire</option>
                        <option value="bank_transfer">Virement bancaire</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label for="payment_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Statut du paiement
                    </label>
                    <select id="payment_status" name="payment_status" class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50">
                        <option value="paid">Payé</option>
                        <option value="unpaid">Non payé</option>
                        <option value="partial">Partiellement payé</option>
                    </select>
                </div>
            </div>
        </div>
        
        <!-- Recherche de produits -->
        <div class="mb-6">
            <label for="product_search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Rechercher un produit
            </label>
            <select id="product_search" class="product-select w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50">
                <option value="">Rechercher un produit par nom ou code-barres</option>
            </select>
        </div>
        
        <!-- Liste des produits -->
        <div class="mb-6">
            <div class="bg-gray-50 dark:bg-gray-700 rounded-t-lg">
                <div class="grid grid-cols-12 gap-4 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                    <div class="col-span-5">Produit</div>
                    <div class="col-span-2">Prix unitaire</div>
                    <div class="col-span-2">Quantité</div>
                    <div class="col-span-2">Total</div>
                    <div class="col-span-1">Action</div>
                </div>
            </div>
            
            <div id="products-container" class="border border-gray-200 dark:border-gray-700 rounded-b-lg">
                <div id="empty-product-list" class="p-4 text-center text-gray-500 dark:text-gray-400">
                    Aucun produit ajouté. Recherchez et ajoutez des produits à la vente.
                </div>
                
                <div id="product-list" class="hidden divide-y divide-gray-200 dark:divide-gray-700"></div>
            </div>
        </div>
        
        <!-- Récapitulatif -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Notes (optionnel)
                </label>
                <textarea id="notes" name="notes" rows="4" class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"></textarea>
            </div>
            
            <!-- Totaux -->
            <div>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-700 dark:text-gray-300">Sous-total:</span>
                        <span id="subtotal" class="font-medium text-gray-800 dark:text-gray-200">0.00 DH</span>
                    </div>
                    
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-700 dark:text-gray-300">Remise:</span>
                        <div class="flex items-center">
                            <input type="number" id="discount" name="discount" value="0" min="0" step="0.01" class="w-20 rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50">
                            <span class="ml-1 text-gray-800 dark:text-gray-200">DH</span>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-gray-700 dark:text-gray-300">TVA:</span>
                        <div class="flex items-center">
                            <input type="number" id="tax" name="tax" value="0" min="0" step="0.01" class="w-20 rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50">
                            <span class="ml-1 text-gray-800 dark:text-gray-200">DH</span>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-200 dark:border-gray-600 pt-2 flex justify-between">
                        <span class="text-lg font-medium text-gray-700 dark:text-gray-300">Total:</span>
                        <span id="total" class="text-lg font-bold text-primary dark:text-primary-dark">0.00 DH</span>
                    </div>
                    
                    <input type="hidden" id="total_amount" name="total_amount" value="0">
                    <input type="hidden" id="final_amount" name="final_amount" value="0">
                </div>
                
                <div class="mt-4 flex justify-end">
                    <button type="button" id="cancel-btn" class="btn-secondary mr-2">
                        Annuler
                    </button>
                    <button type="submit" id="submit-btn" class="btn-primary" disabled>
                        <i class="fas fa-save mr-1"></i> Enregistrer la vente
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Template pour les éléments de produit -->
<template id="product-template">
    <div class="product-item grid grid-cols-12 gap-4 px-4 py-3 items-center text-sm">
        <div class="col-span-5">
            <input type="hidden" name="products[__INDEX__][id]" class="product-id">
            <p class="font-medium text-gray-800 dark:text-gray-200 product-name"></p>
            <p class="text-xs text-gray-500 dark:text-gray-400 product-category"></p>
        </div>
        <div class="col-span-2">
            <input type="number" name="products[__INDEX__][price]" class="product-price w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50" min="0" step="0.01" required>
        </div>
        <div class="col-span-2">
            <input type="number" name="products[__INDEX__][quantity]" class="product-quantity w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50" min="1" value="1" required>
            <p class="text-xs text-gray-500 dark:text-gray-400">Stock: <span class="product-stock"></span></p>
        </div>
        <div class="col-span-2">
            <input type="number" name="products[__INDEX__][discount]" class="product-discount w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50" min="0" step="0.01" value="0">
            <p class="text-xs font-medium text-primary dark:text-primary-dark product-subtotal"></p>
        </div>
        <div class="col-span-1 text-center">
            <button type="button" class="remove-product text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        let productIndex = 0;
        let products = [];
        
        // Initialisation de Select2 pour la recherche de produits
        $('.product-select').select2({
            placeholder: 'Rechercher un produit par nom ou code-barres',
            minimumInputLength: 2,
            ajax: {
                url: '{{ route('sales.search.products') }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        });
        
        // Initialisation de Select2 pour le client
        $('.client-select').select2({
            placeholder: 'Sélectionner un client ou laisser vide pour client anonyme'
        });
        
        // Initialisation de Select2 pour l'ordonnance
        $('.prescription-select').select2({
            placeholder: 'Sélectionner une ordonnance'
        });
        
        // Gestionnaire d'événements pour l'ajout de produit
        $('.product-select').on('select2:select', function(e) {
            const productData = e.params.data;
            addProduct(productData);
            $(this).val(null).trigger('change');
        });
        
        // Fonction d'ajout de produit
        function addProduct(productData) {
            // Vérifier si le produit existe déjà
            const existingProduct = products.find(p => p.id === productData.id);
            if (existingProduct) {
                const quantityInput = $(`#product-${existingProduct.id}`).find('.product-quantity');
                let newQuantity = parseInt(quantityInput.val()) + 1;
                if (newQuantity > productData.stock) {
                    newQuantity = productData.stock;
                    alert('Quantité maximale atteinte pour ce produit !');
                }
                quantityInput.val(newQuantity);
                updateProductSubtotal(existingProduct.id);
                updateTotals();
                return;
            }
            
            // Création d'un nouvel élément de produit
            const template = $('#product-template').html();
            const productItem = template
                .replace(/__INDEX__/g, productIndex)
                .replace('product-item', `product-item product-${productData.id}`);
            
            $('#product-list').append(productItem);
            
            // Mise à jour des valeurs
            const $productItem = $(`.product-${productData.id}`);
            $productItem.find('.product-id').val(productData.id);
            $productItem.find('.product-name').text(productData.name);
            $productItem.find('.product-category').text(productData.category);
            $productItem.find('.product-price').val(productData.price);
            $productItem.find('.product-stock').text(productData.stock);
            
            // Limitation de la quantité au stock disponible
            $productItem.find('.product-quantity').attr('max', productData.stock);
            
            // Calcul initial du sous-total
            updateProductSubtotal(productData.id);
            
            // Ajout du produit à la liste
            products.push({
                id: productData.id,
                index: productIndex,
                name: productData.name,
                price: productData.price,
                stock: productData.stock
            });
            
            // Mise à jour de l'affichage
            $('#empty-product-list').hide();
            $('#product-list').removeClass('hidden');
            $('#submit-btn').prop('disabled', false);
            
            // Mise à jour des totaux
            updateTotals();
            
            // Incrémentation de l'index
            productIndex++;
        }
        
        // Mise à jour du sous-total d'un produit
        function updateProductSubtotal(productId) {
            const $productItem = $(`.product-${productId}`);
            const price = parseFloat($productItem.find('.product-price').val()) || 0;
            const quantity = parseInt($productItem.find('.product-quantity').val()) || 0;
            const discount = parseFloat($productItem.find('.product-discount').val()) || 0;
            
            const subtotal = (price * quantity) - discount;
            $productItem.find('.product-subtotal').text(subtotal.toFixed(2) + ' DH');
        }
        
        // Mise à jour des totaux
        function updateTotals() {
            let subtotal = 0;
            
            // Calcul du sous-total
            $('.product-item').each(function() {
                const price = parseFloat($(this).find('.product-price').val()) || 0;
                const quantity = parseInt($(this).find('.product-quantity').val()) || 0;
                const discount = parseFloat($(this).find('.product-discount').val()) || 0;
                
                subtotal += (price * quantity) - discount;
            });
            
            // Mise à jour de l'affichage
            $('#subtotal').text(subtotal.toFixed(2) + ' DH');
            $('#total_amount').val(subtotal.toFixed(2));
            
            // Calcul du total avec remise et TVA
            const discount = parseFloat($('#discount').val()) || 0;
            const tax = parseFloat($('#tax').val()) || 0;
            
            const finalTotal = subtotal - discount + tax;
            $('#total').text(finalTotal.toFixed(2) + ' DH');
            $('#final_amount').val(finalTotal.toFixed(2));
        }
        
        // Gestionnaire d'événements pour la suppression de produit
        $(document).on('click', '.remove-product', function() {
            const $productItem = $(this).closest('.product-item');
            const productId = $productItem.find('.product-id').val();
            
            // Supprimer le produit de la liste
            products = products.filter(p => p.id !== productId);
            
            // Supprimer l'élément du DOM
            $productItem.remove();
            
            // Mise à jour de l'affichage
            if (products.length === 0) {
                $('#empty-product-list').show();
                $('#product-list').addClass('hidden');
                $('#submit-btn').prop('disabled', true);
            }
            
            // Mise à jour des totaux
            updateTotals();
        });
        
        // Gestionnaire d'événements pour la mise à jour des quantités et prix
        $(document).on('input', '.product-quantity, .product-price, .product-discount', function() {
            const $productItem = $(this).closest('.product-item');
            const productId = $productItem.find('.product-id').val();
            
            // Vérification de la quantité maximum
            if ($(this).hasClass('product-quantity')) {
                const product = products.find(p => p.id === productId);
                const quantity = parseInt($(this).val()) || 0;
                
                if (quantity > product.stock) {
                    $(this).val(product.stock);
                    alert('La quantité ne peut pas dépasser le stock disponible !');
                }
            }
            
            // Mise à jour du sous-total
            updateProductSubtotal(productId);
            
            // Mise à jour des totaux
            updateTotals();
        });
        
        // Gestionnaire d'événements pour la mise à jour des totaux
        $('#discount, #tax').on('input', function() {
            updateTotals();
        });
        
        // Gestionnaire d'événements pour le changement de client
        $('#client_id').on('change', function() {
            const clientId = $(this).val();
            
            if (clientId) {
                // Activer le sélecteur d'ordonnance
                $('#prescription_id').prop('disabled', false);
                
                // Charger les ordonnances du client
                $.ajax({
                    url: '{{ route('sales.client.prescriptions') }}',
                    data: { client_id: clientId },
                    dataType: 'json',
                    success: function(data) {
                        // Réinitialiser le sélecteur
                        $('#prescription_id').empty().append('<option value="">Sélectionner une ordonnance</option>');
                        
                        // Ajouter les options
                        $.each(data.results, function(index, item) {
                            $('#prescription_id').append(new Option(item.text, item.id));
                        });
                    }
                });
            } else {
                // Désactiver le sélecteur d'ordonnance
                $('#prescription_id').prop('disabled', true).empty().append('<option value="">Sélectionner une ordonnance</option>');
            }
        });
        
        // Bouton d'annulation
        $('#cancel-btn').on('click', function() {
            if (confirm('Êtes-vous sûr de vouloir annuler cette vente ? Les données non enregistrées seront perdues.')) {
                window.location.href = '{{ route('sales.index') }}';
            }
        });
        
        // Validation du formulaire
        $('#sale-form').on('submit', function(e) {
            if (products.length === 0) {
                e.preventDefault();
                alert('Veuillez ajouter au moins un produit à la vente !');
                return false;
            }
            
            return true;
        });
    });
</script>
@endpush