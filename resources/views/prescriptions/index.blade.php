@extends('layouts.app')

@section('title', 'Gestion des ordonnances')

@section('header', 'Gestion des ordonnances')

@section('content')
<div class="mb-4 flex justify-between items-center">
    <div>
        <a href="{{ route('prescriptions.create') }}" class="btn-primary">
            <i class="fas fa-plus mr-1"></i> Nouvelle ordonnance
        </a>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg shadow">
    <!-- Filtres -->
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h2 class="font-semibold text-gray-800 dark:text-gray-200 mb-2">Filtres</h2>
        
        <form action="{{ route('prescriptions.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Recherche
                </label>
                <input 
                    type="text" 
                    id="search" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Nom du client ou médecin"
                    class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                >
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Statut
                </label>
                <select 
                    id="status" 
                    name="status" 
                    class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                >
                    <option value="">Tous les statuts</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Traitée</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expirée</option>
                </select>
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
            
            <div class="md:col-span-4 flex justify-end">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-search mr-1"></i> Filtrer
                </button>
                <a href="{{ route('prescriptions.index') }}" class="btn-secondary ml-2">
                    <i class="fas fa-times mr-1"></i> Réinitialiser
                </a>
            </div>
        </form>
    </div>
    
    <!-- Liste des ordonnances -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Client
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Médecin
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Date prescription
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                        Date expiration
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
                @forelse($prescriptions as $prescription)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $prescription->client->name }}
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $prescription->client->phone }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                Dr. {{ $prescription->doctor_name }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                {{ $prescription->getFormattedDate() }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($prescription->expiry_date)
                                @if($prescription->isExpired())
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                        Expiré le {{ $prescription->getFormattedExpiryDate() }}
                                    </span>
                                @else
                                    <div class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ $prescription->getFormattedExpiryDate() }}
                                    </div>
                                @endif
                            @else
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    Non définie
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($prescription->status == 'active')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    Active
                                </span>
                            @elseif($prescription->status == 'completed')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                    Traitée
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                    Expirée
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('prescriptions.show', $prescription) }}" class="text-primary dark:text-primary-dark hover:text-primary-dark dark:hover:text-primary-light mr-3">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('prescriptions.edit', $prescription) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($prescription->status == 'active')
                                <a href="{{ route('sales.create', ['prescription_id' => $prescription->id]) }}" class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 mr-3" title="Créer une vente">
                                    <i class="fas fa-cash-register"></i>
                                </a>
                            @endif
                            <form action="{{ route('prescriptions.destroy', $prescription) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette ordonnance ?')" class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                            Aucune ordonnance trouvée.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
        {{ $prescriptions->withQueryString()->links() }}
    </div>
</div>
@endsection