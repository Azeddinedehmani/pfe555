@extends('layouts.app')

@section('title', isset($prescription) ? 'Modifier une ordonnance' : 'Ajouter une ordonnance')

@section('header', isset($prescription) ? 'Modifier une ordonnance' : 'Ajouter une ordonnance')

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
<div class="mb-4">
    <a href="{{ route('prescriptions.index') }}" class="btn-secondary">
        <i class="fas fa-arrow-left mr-1"></i> Retour à la liste
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg shadow">
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h2 class="font-semibold text-gray-800 dark:text-gray-200">
            {{ isset($prescription) ? 'Modifier l\'ordonnance' : 'Ajouter une nouvelle ordonnance' }}
        </h2>
    </div>
    
    <form action="{{ isset($prescription) ? route('prescriptions.update', $prescription) : route('prescriptions.store') }}" method="POST" enctype="multipart/form-data" class="p-4">
        @csrf
        @if(isset($prescription))
            @method('PUT')
        @endif
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <!-- Informations client et médecin -->
            <div>
                <div class="mb-4">
                    <label for="client_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Client <span class="text-red-600">*</span>
                    </label>
                    <select 
                        id="client_id" 
                        name="client_id" 
                        class="client-select w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                        required
                    >
                        <option value="">Sélectionner un client</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id', isset($prescription) ? $prescription->client_id : '') == $client->id ? 'selected' : '' }}>
                                {{ $client->name }} - {{ $client->phone }}
                            </option>
                        @endforeach
                    </select>
                    @error('client_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="doctor_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nom du médecin <span class="text-red-600">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="doctor_name" 
                        name="doctor_name" 
                        value="{{ old('doctor_name', isset($prescription) ? $prescription->doctor_name : '') }}" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                        required
                    >
                    @error('doctor_name')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="prescription_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Date de prescription <span class="text-red-600">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="prescription_date" 
                        name="prescription_date" 
                        value="{{ old('prescription_date', isset($prescription) && $prescription->prescription_date ? $prescription->prescription_date->format('Y-m-d') : date('Y-m-d')) }}" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                        required
                    >
                    @error('prescription_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="expiry_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Date d'expiration
                    </label>
                    <input 
                        type="date" 
                        id="expiry_date" 
                        name="expiry_date" 
                        value="{{ old('expiry_date', isset($prescription) && $prescription->expiry_date ? $prescription->expiry_date->format('Y-m-d') : '') }}" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                    >
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Laisser vide si l'ordonnance n'a pas de date d'expiration spécifique.
                    </p>
                    @error('expiry_date')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
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
                        <option value="active" {{ old('status', isset($prescription) ? $prescription->status : '') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ old('status', isset($prescription) ? $prescription->status : '') == 'completed' ? 'selected' : '' }}>Traitée</option>
                        <option value="expired" {{ old('status', isset($prescription) ? $prescription->status : '') == 'expired' ? 'selected' : '' }}>Expirée</option>
                    </select>
                    @error('status')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Image de l'ordonnance et notes -->
            <div>
                <div class="mb-4">
                    <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Image de l'ordonnance
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
                    
                    @if(isset($prescription) && $prescription->image)
                        <div class="mt-2">
                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-1">Image actuelle:</p>
                            <img src="{{ asset('storage/' . $prescription->image) }}" alt="Ordonnance" class="h-48 w-auto object-cover rounded">
                        </div>
                    @endif
                </div>
                
                <div class="mb-4">
                    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Notes
                    </label>
                    <textarea 
                        id="notes" 
                        name="notes" 
                        rows="6" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                    >{{ old('notes', isset($prescription) ? $prescription->notes : '') }}</textarea>
                    @error('notes')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 flex justify-end">
            <a href="{{ route('prescriptions.index') }}" class="btn-secondary mr-2">
                Annuler
            </a>
            <button type="submit" class="btn-primary">
                <i class="fas fa-save mr-1"></i> {{ isset($prescription) ? 'Mettre à jour' : 'Enregistrer' }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialisation de Select2 pour le client
        $('.client-select').select2({
            placeholder: 'Sélectionner un client'
        });
        
        // Calcul automatique de la date d'expiration (par défaut +3 mois)
        $('#prescription_date').on('change', function() {
            if (!$('#expiry_date').val()) {
                const prescriptionDate = new Date($(this).val());
                if (!isNaN(prescriptionDate.getTime())) {
                    // Ajouter 3 mois à la date de prescription
                    const expiryDate = new Date(prescriptionDate);
                    expiryDate.setMonth(expiryDate.getMonth() + 3);
                    
                    // Formater la date au format YYYY-MM-DD
                    const year = expiryDate.getFullYear();
                    const month = String(expiryDate.getMonth() + 1).padStart(2, '0');
                    const day = String(expiryDate.getDate()).padStart(2, '0');
                    
                    $('#expiry_date').val(`${year}-${month}-${day}`);
                }
            }
        });
        
        // Déclencher le calcul au chargement si la date d'expiration est vide
        if ($('#prescription_date').val() && !$('#expiry_date').val()) {
            $('#prescription_date').trigger('change');
        }
    });
</script>
@endpush