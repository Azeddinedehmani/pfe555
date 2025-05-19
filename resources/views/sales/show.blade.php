@extends('layouts.app')

@section('title', 'Détails de la vente')

@section('header', 'Détails de la vente')

@section('content')
<div class="mb-4 flex justify-between items-center">
    <div>
        <a href="{{ route('sales.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Retour à la liste
        </a>
    </div>
    <div>
        <a href="{{ route('sales.invoice', $sale) }}" class="btn-primary">
            <i class="fas fa-file-invoice mr-1"></i> Générer facture
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <!-- Informations principales -->
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-4">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h2 class="font-semibold text-gray-800 dark:text-gray-200 flex justify-between items-center">
                    <span>Informations de la vente</span>
                    @if($sale->payment_status == 'paid')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                            Payé
                        </span>
                    @elseif($sale->payment_status == 'unpaid')
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                            Non payé
                        </span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                            Partiellement payé
                        </span>
                    @endif
                </h2>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Référence</p>
                        <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $sale->reference }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Date</p>
                        <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $sale->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Client</p>
                        <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ $sale->client ? $sale->client->name : 'Client anonyme' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Vendeur</p>
                        <p class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $sale->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Mode de paiement</p>
                        <p class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ $sale->payment_method == 'cash' ? 'Espèces' : ($sale->payment_method == 'card' ? 'Carte bancaire' : 'Virement bancaire') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Montant total</p>
                        <p class="text-lg font-bold text-primary dark:text-primary-dark">{{ $sale->getFormattedTotal() }}</p>
                    </div>
                </div>
                
                @if($sale->prescription)
                    <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <p class="text-sm font-medium text-blue-800 dark:text-blue-300">
                            <i class="fas fa-file-medical mr-1"></i> Vente avec ordonnance
                        </p>
                        <p class="text-sm text-blue-600 dark:text-blue-400 mt-1">
                            Docteur: {{ $sale->prescription->doctor_name }} - 
                            Date: {{ $sale->prescription->getFormattedDate() }}
                        </p>
                    </div>
                @endif
                
                @if($sale->notes)
                    <div class="mt-4">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Notes</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $sale->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
        
        <!-- Liste des produits -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h2 class="font-semibold text-gray-800 dark:text-gray-200">Produits achetés</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Produit
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Prix unitaire
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Quantité
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Remise
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Total
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($sale->items as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $item->product->name }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $item->product->category->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ number_format($item->price, 2) }} DH
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ $item->quantity }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ number_format($item->discount, 2) }} DH
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ number_format($item->subtotal, 2) }} DH
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <td colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-300">
                                Sous-total
                            </td>
                            <td class="px-6 py-3 text-right text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ number_format($sale->total_amount, 2) }} DH
                            </td>
                        </tr>
                        @if($sale->discount > 0)
                            <tr>
                                <td colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-300">
                                    Remise
                                </td>
                                <td class="px-6 py-3 text-right text-sm font-medium text-green-600 dark:text-green-400">
                                    - {{ number_format($sale->discount, 2) }} DH
                                </td>
                            </tr>
                        @endif
                        @if($sale->tax > 0)
                            <tr>
                                <td colspan="4" class="px-6 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-300">
                                    TVA
                                </td>
                                <td class="px-6 py-3 text-right text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ number_format($sale->tax, 2) }} DH
                                </td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="4" class="px-6 py-3 text-right text-base font-bold text-gray-800 dark:text-gray-200">
                                Total
                            </td>
                            <td class="px-6 py-3 text-right text-base font-bold text-primary dark:text-primary-dark">
                                {{ number_format($sale->final_amount, 2) }} DH
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Informations latérales -->
    <div>
        <!-- Informations client -->
        @if($sale->client)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-4">
                <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="font-semibold text-gray-800 dark:text-gray-200">Informations client</h2>
                </div>
                <div class="p-4">
                    <div class="mb-3">
                        <p class="text-sm text-gray-500 dark:text-gray-400">Nom</p>
                        <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $sale->client->name }}</p>
                    </div>
                    @if($sale->client->phone)
                        <div class="mb-3">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Téléphone</p>
                            <p class="text-base text-gray-900 dark:text-gray-100">{{ $sale->client->phone }}</p>
                        </div>
                    @endif
                    @if($sale->client->email)
                        <div class="mb-3">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                            <p class="text-base text-gray-900 dark:text-gray-100">{{ $sale->client->email }}</p>
                        </div>
                    @endif
                    @if($sale->client->address)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Adresse</p>
                            <p class="text-base text-gray-900 dark:text-gray-100">{{ $sale->client->address }}</p>
                        </div>
                    @endif
                    
                    <div class="mt-4 border-t border-gray-200 dark:border-gray-700 pt-4">
                        <a href="{{ route('clients.show', $sale->client) }}" class="btn-secondary w-full text-center">
                            <i class="fas fa-user mr-1"></i> Voir le profil client
                        </a>
                    </div>
                </div>
            </div>
        @endif
        
        <!-- Actions -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h2 class="font-semibold text-gray-800 dark:text-gray-200">Actions</h2>
            </div>
            <div class="p-4 space-y-3">
                <a href="{{ route('sales.invoice', $sale) }}" class="btn-primary w-full text-center flex items-center justify-center">
                    <i class="fas fa-file-invoice mr-1"></i> Télécharger la facture
                </a>
                
                @if($sale->payment_status != 'paid')
                    <button type="button" class="btn-success w-full text-center">
                        <i class="fas fa-check-circle mr-1"></i> Marquer comme payé
                    </button>
                @endif
                
                <form action="{{ route('sales.destroy', $sale) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette vente ?')" class="btn-danger w-full text-center">
                        <i class="fas fa-trash mr-1"></i> Supprimer la vente
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection