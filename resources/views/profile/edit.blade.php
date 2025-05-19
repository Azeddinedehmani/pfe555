@extends('layouts.app')

@section('title', 'Mon profil')

@section('header', 'Mon profil')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <!-- Informations générales -->
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h2 class="font-semibold text-gray-800 dark:text-gray-200">Informations du profil</h2>
            </div>
            
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="p-4">
                @csrf
                @method('PATCH')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="md:col-span-2 flex items-center">
                        <div class="flex-shrink-0 h-24 w-24 mr-4">
                            <img class="h-24 w-24 rounded-full object-cover" src="{{ $user->getAvatar() }}" alt="{{ $user->name }}">
                        </div>
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Changer la photo de profil
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
                        </div>
                    </div>
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nom complet
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="{{ old('name', $user->name) }}" 
                            class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                            required
                        >
                        @error('name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Adresse email
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email', $user->email) }}" 
                            class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                            required
                        >
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Téléphone
                        </label>
                        <input 
                            type="text" 
                            id="phone" 
                            name="phone" 
                            value="{{ old('phone', $user->phone) }}" 
                            class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                        >
                        @error('phone')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Adresse
                        </label>
                        <textarea 
                            id="address" 
                            name="address" 
                            rows="3" 
                            class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                        >{{ old('address', $user->address) }}</textarea>
                        @error('address')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-1"></i> Mettre à jour le profil
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Changement de mot de passe -->
    <div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h2 class="font-semibold text-gray-800 dark:text-gray-200">Changer le mot de passe</h2>
            </div>
            
            <form action="{{ route('profile.update.password') }}" method="POST" class="p-4">
                @csrf
                @method('PATCH')
                
                <div class="mb-4">
                    <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Mot de passe actuel
                    </label>
                    <input 
                        type="password" 
                        id="current_password" 
                        name="current_password" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                        required
                    >
                    @error('current_password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Nouveau mot de passe
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                        required
                    >
                    @error('password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Confirmer le nouveau mot de passe
                    </label>
                    <input 
                        type="password" 
                        id="password_confirmation" 
                        name="password_confirmation" 
                        class="w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary dark:focus:border-primary-dark focus:ring focus:ring-primary dark:focus:ring-primary-dark focus:ring-opacity-50"
                        required
                    >
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-key mr-1"></i> Changer le mot de passe
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Informations du compte -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow mt-4">
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <h2 class="font-semibold text-gray-800 dark:text-gray-200">Informations du compte</h2>
            </div>
            
            <div class="p-4">
                <div class="mb-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Rôle</p>
                    <p class="text-base font-medium text-gray-900 dark:text-gray-100 capitalize">{{ $user->role }}</p>
                </div>
                
                <div class="mb-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Statut</p>
                    <p class="text-base font-medium text-gray-900 dark:text-gray-100 capitalize">{{ $user->status }}</p>
                </div>
                
                <div class="mb-3">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Date d'inscription</p>
                    <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $user->created_at->format('d/m/Y') }}</p>
                </div>
                
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Dernière mise à jour</p>
                    <p class="text-base font-medium text-gray-900 dark:text-gray-100">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection