@extends('layouts.app')

@section('title', 'Rapport des ventes')

@section('header', 'Rapport des ventes')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css">
<style>
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
</style>
@endpush

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
    <!-- Filtres -->
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h2 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">Filtres</h2>
        
        <form action="{{ route('reports.sales') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Date début
                </label>
                <input 
                    type="date" 
                    id="start_date" 
                    name="start_date" 
                    value="{{ $start_date }}" 
                    class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                >
            </div>
            
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Date fin
                </label>
                <input 
                    type="date" 
                    id="end_date" 
                    name="end_date" 
                    value="{{ $end_date }}" 
                    class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                >
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
            
            <div class="md:col-span-4 flex justify-end">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-search mr-1"></i> Générer le rapport
                </button>
                <a href="{{ route('reports.sales') }}" class="btn-secondary ml-2">
                    <i class="fas fa-times mr-1"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Statistiques générales -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                <i class="fas fa-shopping-cart text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Nombre de ventes</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalSales }}</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                <i class="fas fa-money-bill-wave text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Revenu total</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($totalRevenue, 2) }} DH</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400">
                <i class="fas fa-chart-line text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Valeur moyenne</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($averageRevenue, 2) }} DH</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="flex items-center">
            <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600 dark:text-yellow-400">
                <i class="fas fa-calendar-alt text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Période</p>
                <p class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    {{ \Carbon\Carbon::parse($start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Graphiques -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Graphique des ventes par jour -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
            <h2 class="font-semibold text-gray-800 dark:text-gray-200">Ventes par jour</h2>
        </div>
        <div class="p-4">
            <div class="chart-container">
                <canvas id="salesByDayChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Graphique par méthode de paiement -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
            <h2 class="font-semibold text-gray-800 dark:text-gray-200">Ventes par méthode de paiement</h2>
        </div>
        <div class="p-4">
            <div class="chart-container">
                <canvas id="paymentMethodChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Produits les plus vendus -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h2 class="font-semibold text-gray-800 dark:text-gray-200">Produits les plus vendus</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Produit
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Quantité vendue
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Revenu généré
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($topProducts as $product)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $product['product']->name }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $product['product']->category->name }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $product['quantity'] }} unités
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ number_format($product['revenue'], 2) }} DH
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                            Aucun produit vendu durant cette période.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Liste des ventes -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow">
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h2 class="font-semibold text-gray-800 dark:text-gray-200">Détails des ventes</h2>
    </div>
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
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Montant
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
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
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                {{ $sale->client ? $sale->client->name : 'Client anonyme' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                {{ $sale->created_at->format('d/m/Y H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ number_format($sale->final_amount, 2) }} DH
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
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
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('sales.show', $sale) }}" class="text-primary dark:text-primary-dark hover:text-primary-dark dark:hover:text-primary-light">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                            Aucune vente trouvée pour cette période.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Données pour le graphique par jour
        const salesByDayLabels = {!! json_encode(array_keys($salesByDay->toArray())) !!};
        const salesByDayData = {!! json_encode(array_column($salesByDay->toArray(), 'revenue')) !!};
        
        // Données pour le graphique par méthode de paiement
        const paymentMethodLabels = {!! json_encode(array_map(function($method) {
            return $method === 'cash' ? 'Espèces' : ($method === 'card' ? 'Carte bancaire' : 'Virement bancaire');
        }, array_keys($salesByPaymentMethod->toArray()))) !!};
        const paymentMethodData = {!! json_encode(array_column($salesByPaymentMethod->toArray(), 'revenue')) !!};
        
        // Couleurs pour les graphiques
        const chartColors = {
            blue: '#4a90e2',
            green: '#5cb85c',
            purple: '#9966cc',
            orange: '#f0ad4e',
            red: '#d9534f'
        };
        
        // Graphique des ventes par jour
        const salesByDayCtx = document.getElementById('salesByDayChart').getContext('2d');
        const salesByDayChart = new Chart(salesByDayCtx, {
            type: 'line',
            data: {
                labels: salesByDayLabels,
                datasets: [{
                    label: 'Revenu par jour',
                    data: salesByDayData,
                    backgroundColor: chartColors.blue,
                    borderColor: chartColors.blue,
                    borderWidth: 2,
                    tension: 0.3,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + ' DH';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw + ' DH';
                            }
                        }
                    }
                }
            }
        });
        
        // Graphique par méthode de paiement
        const paymentMethodCtx = document.getElementById('paymentMethodChart').getContext('2d');
        const paymentMethodChart = new Chart(paymentMethodCtx, {
            type: 'pie',
            data: {
                labels: paymentMethodLabels,
                datasets: [{
                    data: paymentMethodData,
                    backgroundColor: [
                        chartColors.green,
                        chartColors.blue,
                        chartColors.purple
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw + ' DH';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush