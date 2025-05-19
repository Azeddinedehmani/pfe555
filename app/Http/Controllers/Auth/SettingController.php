<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Afficher la page des paramètres.
     */
    public function index()
    {
        $this->authorize('viewSettings');
        
        // Récupérer tous les paramètres par groupe
        $generalSettings = Setting::where('group', 'general')->get()->keyBy('key');
        $invoiceSettings = Setting::where('group', 'invoice')->get()->keyBy('key');
        $systemSettings = Setting::where('group', 'system')->get()->keyBy('key');
        
        return view('settings.index', compact('generalSettings', 'invoiceSettings', 'systemSettings'));
    }

    /**
     * Mettre à jour les paramètres.
     */
    public function update(Request $request)
    {
        $this->authorize('updateSettings');
        
        // Valider les données
        $request->validate([
            'settings.*.value' => 'nullable',
            'settings.*.group' => 'required|in:general,invoice,system',
            'pharmacy_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        // Paramètres envoyés
        $settings = $request->input('settings', []);
        
        // Traiter l'image du logo si fournie
        if ($request->hasFile('pharmacy_logo')) {
            $logoPath = $request->file('pharmacy_logo')->store('settings', 'public');
            $settings['pharmacy_logo'] = [
                'value' => $logoPath,
                'group' => 'general'
            ];
            
            // Supprimer l'ancien logo s'il existe
            $oldLogo = Setting::where('key', 'pharmacy_logo')->first();
            if ($oldLogo && $oldLogo->value) {
                Storage::disk('public')->delete($oldLogo->value);
            }
        }
        
        // Mettre à jour ou créer chaque paramètre
        foreach ($settings as $key => $data) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $data['value'], 'group' => $data['group']]
            );
        }
        
        // Enregistrement de l'activité
        activity_log('update', null, 'A mis à jour les paramètres du système');
        
        return redirect()->route('settings.index')
            ->with('success', 'Paramètres mis à jour avec succès.');
    }
    
    /**
     * Créer une sauvegarde de la base de données.
     */
    public function backup()
    {
        $this->authorize('manageBackup');
        
        try {
            // Exécuter la commande de sauvegarde
            Artisan::call('backup:run --only-db');
            
            // Enregistrement de l'activité
            activity_log('create', null, 'A créé une sauvegarde de la base de données');
            
            return redirect()->route('settings.index')
                ->with('success', 'Sauvegarde créée avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('settings.index')
                ->with('error', 'Erreur lors de la création de la sauvegarde: ' . $e->getMessage());
        }
    }
    
    /**
     * Restaurer la base de données à partir d'une sauvegarde.
     */
    public function restore(Request $request)
    {
        $this->authorize('manageBackup');
        
        $request->validate([
            'backup_file' => 'required|file',
        ]);
        
        try {
            // Enregistrer le fichier temporairement
            $file = $request->file('backup_file');
            $path = $file->storeAs('temp', 'restore.sql');
            
            // Exécuter la commande de restauration
            // Note: Ceci est un exemple simplifié. Dans une application réelle, 
            // vous devriez utiliser un package de sauvegarde/restauration comme spatie/laravel-backup
            // et implémenter une logique plus robuste
            Artisan::call('db:restore', ['path' => Storage::path($path)]);
            
            // Supprimer le fichier temporaire
            Storage::delete($path);
            
            // Enregistrement de l'activité
            activity_log('update', null, 'A restauré la base de données');
            
            return redirect()->route('settings.index')
                ->with('success', 'Base de données restaurée avec succès.');
        } catch (\Exception $e) {
            return redirect()->route('settings.index')
                ->with('error', 'Erreur lors de la restauration: ' . $e->getMessage());
        }
    }
}