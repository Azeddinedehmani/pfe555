@extends('layouts.app')

@section('title', isset($category) ? 'Modifier une catégorie' : 'Ajouter une catégorie')

@section('header', isset($category) ? 'Modifier une catégorie' : 'Ajouter une catégorie')

@section('content')
<div class="mb-4">
    <a href="{{ route('categories.index') }}" class="btn-secondary">
        <i class="fas fa-arrow-left mr-1"></i> Retour à la liste
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-lg shadow">
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h2 class="font-semibold text-gray-800 dark:text-gray-200">
            {{ isset($category) ? 'Modifier la catégorie: ' . $category->name : 'Ajouter une nouvelle catégorie' }}
        </h2>
    </div>
    
    <form action="{{ isset($category) ? route('categories.update', $category) : route('categories.store') }}" method="POST" class="p-4">
        @csrf
        @if(isset($category))
            @method('PUT')
        @endif
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <!-- Informations de base -->
            <div>
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nom de la catégorie <span class="text-red-600">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', isset($category) ? $category->name : '') }}" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                        required
                    >
                    @error('name')
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
                    >{{ old('description', isset($category) ? $category->description : '') }}</textarea>
                    @error('description')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <!-- Icône et statut -->
            <div>
                <div class="mb-4">
                    <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Icône (nom Font Awesome)
                    </label>
                    <div class="flex">
                        <div class="relative flex items-center">
                            <span class="absolute left-3 text-gray-500 dark:text-gray-400">
                                <i class="fas fa-fw"></i>
                            </span>
                            <input 
                                type="text" 
                                id="icon" 
                                name="icon" 
                                value="{{ old('icon', isset($category) ? $category->icon : '') }}" 
                                placeholder="box, tag, pills, etc."
                                class="pl-10 w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                            >
                        </div>
                        <div class="ml-2 flex items-center">
                            <span class="p-2 bg-gray-100 dark:bg-gray-700 rounded-md">
                                <i id="icon-preview" class="fas fa-{{ old('icon', isset($category) ? $category->icon : 'tag') }} text-primary dark:text-primary-dark"></i>
                            </span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Entrez un nom d'icône Font Awesome sans le préfixe "fa-". Par exemple: "tag", "box", "pills"
                    </p>
                    @error('icon')
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
                        <option value="active" {{ old('status', isset($category) ? $category->status : '') == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ old('status', isset($category) ? $category->status : '') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                    </select>
                    @error('status')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 flex justify-end">
            <a href="{{ route('categories.index') }}" class="btn-secondary mr-2">
                Annuler
            </a>
            <button type="submit" class="btn-primary">
                <i class="fas fa-save mr-1"></i> {{ isset($category) ? 'Mettre à jour' : 'Enregistrer' }}
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const iconInput = document.getElementById('icon');
        const iconPreview = document.getElementById('icon-preview');
        
        iconInput.addEventListener('input', function() {
            const iconName = this.value.trim() || 'tag';
            iconPreview.className = `fas fa-${iconName} text-primary dark:text-primary-dark`;
        });
    });
</script>
@endpush