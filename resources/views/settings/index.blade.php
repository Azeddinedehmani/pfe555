@extends('layouts.app')

@section('title', 'Paramètres du système')

@section('header', 'Paramètres du système')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-lg shadow">
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h2 class="font-semibold text-gray-800 dark:text-gray-200">Configuration de l'application</h2>
    </div>
    
    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="p-4">
        @csrf
        
        <!-- Paramètres généraux -->
        <div class="mb-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">Informations de la pharmacie</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="pharmacy_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nom de la pharmacie
                    </label>
                    <input 
                        type="text" 
                        id="pharmacy_name" 
                        name="settings[pharmacy_name][value]" 
                        value="{{ $generalSettings['pharmacy_name']->value ?? '' }}" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                    >
                    <input type="hidden" name="settings[pharmacy_name][group]" value="general">
                </div>
                
                <div>
                    <label for="pharmacy_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Téléphone
                    </label>
                    <input 
                        type="text" 
                        id="pharmacy_phone" 
                        name="settings[pharmacy_phone][value]" 
                        value="{{ $generalSettings['pharmacy_phone']->value ?? '' }}" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                    >
                    <input type="hidden" name="settings[pharmacy_phone][group]" value="general">
                </div>
                
                <div>
                    <label for="pharmacy_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Email
                    </label>
                    <input 
                        type="email" 
                        id="pharmacy_email" 
                        name="settings[pharmacy_email][value]" 
                        value="{{ $generalSettings['pharmacy_email']->value ?? '' }}" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                    >
                    <input type="hidden" name="settings[pharmacy_email][group]" value="general">
                </div>
                
                <div>
                    <label for="pharmacy_tax_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Numéro d'identification fiscale
                    </label>
                    <input 
                        type="text" 
                        id="pharmacy_tax_id" 
                        name="settings[pharmacy_tax_id][value]" 
                        value="{{ $generalSettings['pharmacy_tax_id']->value ?? '' }}" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                    >
                    <input type="hidden" name="settings[pharmacy_tax_id][group]" value="general">
                </div>
                
                <div class="md:col-span-2">
                    <label for="pharmacy_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Adresse
                    </label>
                    <textarea 
                        id="pharmacy_address" 
                        name="settings[pharmacy_address][value]" 
                        rows="2" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                    >{{ $generalSettings['pharmacy_address']->value ?? '' }}</textarea>
                    <input type="hidden" name="settings[pharmacy_address][group]" value="general">
                </div>
                
                <div class="md:col-span-2">
                    <label for="pharmacy_logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Logo
                    </label>
                    <input 
                        type="file" 
                        id="pharmacy_logo" 
                        name="pharmacy_logo" 
                        accept="image/*" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                    >
                    
                    @if(isset($generalSettings['pharmacy_logo']) && $generalSettings['pharmacy_logo']->value)
                        <div class="mt-2 flex items-center">
                            <img src="{{ asset('storage/' . $generalSettings['pharmacy_logo']->value) }}" alt="Logo" class="h-12 w-auto object-contain">
                            <span class="ml-2 text-sm text-gray-500 dark:text-gray-400">Logo actuel</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Paramètres de facturation -->
        <div class="mb-6 border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">Paramètres de facturation</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="invoice_prefix" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Préfixe des factures
                    </label>
                    <input 
                        type="text" 
                        id="invoice_prefix" 
                        name="settings[invoice_prefix][value]" 
                        value="{{ $invoiceSettings['invoice_prefix']->value ?? 'INV-' }}" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                    >
                    <input type="hidden" name="settings[invoice_prefix][group]" value="invoice">
                </div>
                
                <div>
                    <label for="invoice_tax_rate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Taux de TVA par défaut (%)
                    </label>
                    <input 
                        type="number" 
                        id="invoice_tax_rate" 
                        name="settings[invoice_tax_rate][value]" 
                        value="{{ $invoiceSettings['invoice_tax_rate']->value ?? '0' }}" 
                        step="0.01" 
                        min="0" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                    >
                    <input type="hidden" name="settings[invoice_tax_rate][group]" value="invoice">
                </div>
                
                <div>
                    <label for="invoice_due_days" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Délai de paiement par défaut (jours)
                    </label>
                    <input 
                        type="number" 
                        id="invoice_due_days" 
                        name="settings[invoice_due_days][value]" 
                        value="{{ $invoiceSettings['invoice_due_days']->value ?? '30' }}" 
                        min="0" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                    >
                    <input type="hidden" name="settings[invoice_due_days][group]" value="invoice">
                </div>
                
                <div>
                    <label for="invoice_footer_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Texte de pied de page
                    </label>
                    <input 
                        type="text" 
                        id="invoice_footer_text" 
                        name="settings[invoice_footer_text][value]" 
                        value="{{ $invoiceSettings['invoice_footer_text']->value ?? 'Merci pour votre confiance!' }}" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                    >
                    <input type="hidden" name="settings[invoice_footer_text][group]" value="invoice">
                </div>
                
                <div class="md:col-span-2">
                    <label for="invoice_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Notes par défaut
                    </label>
                    <textarea 
                        id="invoice_notes" 
                        name="settings[invoice_notes][value]" 
                        rows="2" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                    >{{ $invoiceSettings['invoice_notes']->value ?? '' }}</textarea>
                    <input type="hidden" name="settings[invoice_notes][group]" value="invoice">
                </div>
            </div>
        </div>
        
        <!-- Paramètres du système -->
        <div class="mb-6 border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-3">Paramètres du système</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="system_default_pagination" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Pagination par défaut
                    </label>
                    <input 
                        type="number" 
                        id="system_default_pagination" 
                        name="settings[system_default_pagination][value]" 
                        value="{{ $systemSettings['system_default_pagination']->value ?? '15' }}" 
                        min="5" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                    >
                    <input type="hidden" name="settings[system_default_pagination][group]" value="system">
                </div>
                
                <div>
                    <label for="system_date_format" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Format de date
                    </label>
                    <select 
                        id="system_date_format" 
                        name="settings[system_date_format][value]" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                    >
                        <option value="d/m/Y" {{ ($systemSettings['system_date_format']->value ?? 'd/m/Y') == 'd/m/Y' ? 'selected' : '' }}>31/12/2023 (JJ/MM/AAAA)</option>
                        <option value="m/d/Y" {{ ($systemSettings['system_date_format']->value ?? '') == 'm/d/Y' ? 'selected' : '' }}>12/31/2023 (MM/JJ/AAAA)</option>
                        <option value="Y-m-d" {{ ($systemSettings['system_date_format']->value ?? '') == 'Y-m-d' ? 'selected' : '' }}>2023-12-31 (AAAA-MM-JJ)</option>
                        <option value="d-m-Y" {{ ($systemSettings['system_date_format']->value ?? '') == 'd-m-Y' ? 'selected' : '' }}>31-12-2023 (JJ-MM-AAAA)</option>
                    </select>
                    <input type="hidden" name="settings[system_date_format][group]" value="system">
                </div>
                
                <div>
                    <label for="system_currency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Devise
                    </label>
                    <input 
                        type="text" 
                        id="system_currency" 
                        name="settings[system_currency][value]" 
                        value="{{ $systemSettings['system_currency']->value ?? 'DH' }}" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                    >
                    <input type="hidden" name="settings[system_currency][group]" value="system">
                </div>
                
                <div>
                    <label for="system_backup_frequency" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Fréquence de sauvegarde
                    </label>
                    <select 
                        id="system_backup_frequency" 
                        name="settings[system_backup_frequency][value]" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                    >
                        <option value="daily" {{ ($systemSettings['system_backup_frequency']->value ?? 'weekly') == 'daily' ? 'selected' : '' }}>Quotidienne</option>
                        <option value="weekly" {{ ($systemSettings['system_backup_frequency']->value ?? 'weekly') == 'weekly' ? 'selected' : '' }}>Hebdomadaire</option>
                        <option value="monthly" {{ ($systemSettings['system_backup_frequency']->value ?? '') == 'monthly' ? 'selected' : '' }}>Mensuelle</option>
                    </select>
                    <input type="hidden" name="settings[system_backup_frequency][group]" value="system">
                </div>
            </div>
        </div>
        
        <div class="flex justify-end">
            <a href="{{ route('dashboard') }}" class="btn-secondary mr-2">
                Annuler
            </a>
            <button type="submit" class="btn-primary">
                <i class="fas fa-save mr-1"></i> Enregistrer les paramètres
            </button>
        </div>
    </form>
</div>

<!-- Sauvegarde et restauration -->
<div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
            <h2 class="font-semibold text-gray-800 dark:text-gray-200">Sauvegarde de la base de données</h2>
        </div>
        <div class="p-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Créez une sauvegarde de la base de données pour éviter toute perte de données. Nous vous recommandons de faire des sauvegardes régulières.
            </p>
            
            <form action="{{ route('backup') }}" method="GET">
                @csrf
                <button type="submit" class="btn-primary w-full">
                    <i class="fas fa-download mr-1"></i> Créer une sauvegarde
                </button>
            </form>
        </div>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
            <h2 class="font-semibold text-gray-800 dark:text-gray-200">Restauration</h2>
        </div>
        <div class="p-4">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Restaurez une sauvegarde précédente. Attention : cette action remplacera toutes les données actuelles par celles de la sauvegarde.
            </p>
            
            <form action="{{ route('restore') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="backup_file" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Fichier de sauvegarde
                    </label>
                    <input 
                        type="file" 
                        id="backup_file" 
                        name="backup_file" 
                        accept=".sql" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                        required
                    >
                </div>
                
                <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir restaurer cette sauvegarde ? Toutes les données actuelles seront remplacées.')" class="btn-warning w-full">
                    <i class="fas fa-upload mr-1"></i> Restaurer
                </button>
            </form>
        </div>
    </div>
</div>
@endsection