@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('header', 'Tableau de bord')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Statistiques des ventes -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Ventes aujourd'hui</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $todaySales }}</p>
            </div>
            <div class="p-3 bg-blue-100 dark:bg-blue-900/30 text-primary dark:text-primary-dark rounded-full">
                <i class="fas fa-shopping-cart text-xl"></i>
            </div>
        </div>
        <p class="text-sm text-green-600 dark:text-green-400 mt-2">
            <span>{{ number_format($todayRevenue, 2) }} DH</span>
        </p>
    </div>

    <!-- Statistiques des produits -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total des produits</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $totalProducts }}</p>
            </div>
            <div class="p-3 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-full">
                <i class="fas fa-pills text-xl"></i>
            </div>
        </div>
        <p class="text-sm text-yellow-600 dark:text-yellow-400 mt-2">
            <i class="fas fa-exclamation-triangle"></i>
            <span>{{ $lowStockProducts }} produits en stock faible</span>
        </p>
    </div>

    <!-- Statistiques des clients -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total des clients</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $totalClients }}</p>
            </div>
            <div class="p-3 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-full">
                <i class="fas fa-users text-xl"></i>
            </div>
        </div>
        <p class="text-sm text-blue-600 dark:text-blue-400 mt-2">
            <i class="fas fa-file-medical"></i>
            <span>{{ $expiringPrescriptions }} ordonnances à suivre</span>
        </p>
    </div>

    <!-- Statistiques des ventes mensuelles -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Ventes ce mois</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $monthSales }}</p>
            </div>
            <div class="p-3 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-full">
                <i class="fas fa-chart-line text-xl"></i>
            </div>
        </div>
        <p class="text-sm text-blue-600 dark:text-blue-400 mt-2">
            <span>{{ number_format($monthRevenue, 2) }} DH</span>
        </p>
    </div>
</div>

<!-- Alertes et actions rapides -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
    <!-- Produits à faible stock -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 flex items-center justify-between">
            <h2 class="font-semibold text-gray-800 dark:text-gray-200">Produits à faible stock</h2>
            <a href="{{ route('products.low.stock') }}" class="text-sm text-primary dark:text-primary-dark hover:underline">
                Voir tout
            </a>
        </div>
        <div class="p-4">
            @if($criticalProducts->count() > 0)
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($criticalProducts as $product)
                        <li class="py-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ $product->name }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $product->category->name }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-red-600 dark:text-red-400">
                                        {{ $product->quantity }} en stock
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Alerte: {{ $product->alert_quantity }}
                                    </p>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400 py-2">
                    Aucun produit en stock critique.
                </p>
            @endif
        </div>
    </div>

    <!-- Dernières ventes -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 flex items-center justify-between">
            <h2 class="font-semibold text-gray-800 dark:text-gray-200">Dernières ventes</h2>
            <a href="{{ route('sales.index') }}" class="text-sm text-primary dark:text-primary-dark hover:underline">
                Voir tout
            </a>
        </div>
        <div class="p-4">
            @if($recentSales->count() > 0)
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($recentSales as $sale)
                        <li class="py-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ $sale->reference }}
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $sale->client ? $sale->client->name : 'Client anonyme' }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium {{ $sale->isPaid() ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }}">
                                        {{ $sale->getFormattedTotal() }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $sale->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400 py-2">
                    Aucune vente récente.
                </p>
            @endif
        </div>
    </div>

    <!-- Dernières activités -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="border-b border-gray-200 dark:border-gray-700 px-4 py-3 flex items-center justify-between">
            <h2 class="font-semibold text-gray-800 dark:text-gray-200">Activités récentes</h2>
            @if(auth()->user()->isResponsable())
                <a href="{{ route('activity.logs') }}" class="text-sm text-primary dark:text-primary-dark hover:underline">
                    Voir tout
                </a>
            @endif
        </div>
        <div class="p-4">
            @if($recentActivities->count() > 0)
                <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($recentActivities as $activity)
                        <li class="py-2">
                            <div>
                                <h3 class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                    {{ $activity->user->name }}
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $activity->action }} - {{ $activity->model_type }} - {{ $activity->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400 py-2">
                    Aucune activité récente.
                </p>
            @endif
        </div>
    </div>
</div>

<!-- Boutons d'action rapide -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <a href="{{ route('sales.create') }}" class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex items-center transition hover:shadow-md">
        <div class="p-3 bg-blue-100 dark:bg-blue-900/30 text-primary dark:text-primary-dark rounded-full">
            <i class="fas fa-cash-register text-xl"></i>
        </div>
        <div class="ml-4">
            <h3 class="font-medium text-gray-800 dark:text-gray-200">Nouvelle vente</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Enregistrer une transaction</p>
        </div>
    </a>
    
    <a href="{{ route('products.create') }}" class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex items-center transition hover:shadow-md">
        <div class="p-3 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-full">
            <i class="fas fa-pills text-xl"></i>
        </div>
        <div class="ml-4">
            <h3 class="font-medium text-gray-800 dark:text-gray-200">Ajouter un produit</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Gérer l'inventaire</p>
        </div>
    </a>
    
    <a href="{{ route('clients.create') }}" class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex items-center transition hover:shadow-md">
        <div class="p-3 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-full">
            <i class="fas fa-user-plus text-xl"></i>
        </div>
        <div class="ml-4">
            <h3 class="font-medium text-gray-800 dark:text-gray-200">Nouveau client</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Ajouter un client</p>
        </div>
    </a>
    
    <a href="{{ route('prescriptions.create') }}" class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 flex items-center transition hover:shadow-md">
        <div class="p-3 bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 rounded-full">
            <i class="fas fa-file-medical text-xl"></i>
        </div>
        <div class="ml-4">
            <h3 class="font-medium text-gray-800 dark:text-gray-200">Nouvelle ordonnance</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Enregistrer une prescription</p>
        </div>
    </a>
</div>

<!-- Graphique des ventes (Placeholder) -->
<div class="bg-white dark:bg-gray-800 rounded-lg shadow mb-6">
    <div class="border-b border-gray-200 dark:border-gray-700 px-4 py-3">
        <h2 class="font-semibold text-gray-800 dark:text-gray-200">Évolution des ventes</h2>
    </div>
    <div class="p-4 h-80 flex items-center justify-center">
        <p class="text-gray-500 dark:text-gray-400">
            Le graphique des ventes sera affiché ici...
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Code pour initialiser les graphiques serait ajouté ici
</script>
@endpush