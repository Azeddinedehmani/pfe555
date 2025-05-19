@extends('layouts.app')

@section('title', 'Gestion des ventes')

@section('header', 'Gestion des ventes')

@section('content')
<div class="mb-4 flex justify-between items-center">
    <div>
        <a href="{{ route('sales.create') }}" class="btn-primary">
            <i class="fas fa-plus mr-1"></i> Nouvelle vente
        </a>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg shadow">
    <!-- Filtres -->
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h2 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">Filtres</h2>
        
        <form action="{{ route('sales.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Référence
                </label>
                <input 
                    type="text" 
                    id="search" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Rechercher par référence"
                    class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                >
            </div>
            
            <div>
                <label for="client_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Client
                </label>
                <select 
                    id="client_id" 
                    name="client_id" 
                    class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                >
                    <option value="">Tous les clients</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="payment_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Statut de paiement
                </label>
                <select 
                    id="payment_status" 
                    name="payment_status" 
                    class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                >
                    <option value="">Tous les statuts</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Payé</option>
                    <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Non payé</option>
                    <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>Partiellement payé</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-search mr-1"></i> Filtrer
                </button>
                <a href="{{ route('sales.index') }}" class="btn-secondary ml-2">
                    <i class="fas fa-times mr-1"></i> Réinitialiser
                </a>
            </div>
            
            <div>
                <label for="date_start" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Date début
                </label>
                <input 
                    type="date" 
                    id="date_start" 
                    name="date_start" 
                    value="{{ request('date_start') }}" 
                    class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                >
            </div>
            
            <div>
                <label for="date_end" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Date fin
                </label>
                <input 
                    type="date" 
                    id="date_end" 
                    name="date_end" 
                    value="{{ request('date_end') }}" 
                    class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                >
            </div>
        </form>
    </div>
    
    <!-- Liste des ventes -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Référence
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Client
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Date
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Montant
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Paiement
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($sales as $sale)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $sale->reference }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                Par: {{ $sale->user->name }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                {{ $sale->client ? $sale->client->name : 'Client anonyme' }}
                            </div>
                            @if($sale->prescription)
                                <div class="text-xs text-blue-600 dark:text-blue-400">
                                    <i class="fas fa-file-medical mr-1"></i> Avec ordonnance
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                {{ $sale->created_at->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $sale->created_at->format('H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $sale->getFormattedTotal() }}
                            </div>
                            @if($sale->discount > 0)
                                <div class="text-xs text-green-600 dark:text-green-400">
                                    Remise: {{ number_format($sale->discount, 2) }} DH
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
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
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('sales.show', $sale) }}" class="text-primary dark:text-primary-dark hover:text-primary-dark dark:hover:text-primary-light mr-2">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('sales.invoice', $sale) }}" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 mr-2">
                                <i class="fas fa-file-invoice"></i>
                            </a>
                            <form action="{{ route('sales.destroy', $sale) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette vente ?')" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                            Aucune vente trouvée.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
        {{ $sales->withQueryString()->links() }}
    </div>
</div>
@endsection