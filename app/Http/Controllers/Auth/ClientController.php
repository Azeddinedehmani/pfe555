<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    /**
     * Afficher la liste des clients.
     */
    public function index(Request $request)
    {
        $query = Client::query();
        
        // Filtrage par statut
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Recherche par nom, email ou téléphone
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        $clients = $query->latest()->paginate(15);
        
        return view('clients.index', compact('clients'));
    }

    /**
     * Afficher le formulaire de création.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Enregistrer un nouveau client.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:clients,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);
        
        $client = Client::create($validated);
        
        // Enregistrement de l'activité
        activity_log('create', $client, 'A créé un nouveau client: ' . $client->name);
        
        return redirect()->route('clients.index')
            ->with('success', 'Client créé avec succès.');
    }

    /**
     * Afficher les détails d'un client.
     */
    public function show(Client $client)
    {
        $client->load(['prescriptions' => function($query) {
            $query->latest()->take(5);
        }, 'sales' => function($query) {
            $query->latest()->take(5);
        }]);
        
        return view('clients.show', compact('client'));
    }

    /**
     * Afficher le formulaire de modification.
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Mettre à jour un client.
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'nullable',
                'email',
                Rule::unique('clients')->ignore($client->id),
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);
        
        $client->update($validated);
        
        // Enregistrement de l'activité
        activity_log('update', $client, 'A mis à jour le client: ' . $client->name);
        
        return redirect()->route('clients.index')
            ->with('success', 'Client mis à jour avec succès.');
    }

    /**
     * Supprimer un client.
     */
    public function destroy(Client $client)
    {
        // Vérifier si le client a des ventes ou des ordonnances
        if ($client->sales()->count() > 0 || $client->prescriptions()->count() > 0) {
            return redirect()->route('clients.index')
                ->with('error', 'Ce client ne peut pas être supprimé car il a des ventes ou des ordonnances associées.');
        }
        
        $clientName = $client->name;
        $client->delete();
        
        // Enregistrement de l'activité
        activity_log('delete', $client, 'A supprimé le client: ' . $clientName);
        
        return redirect()->route('clients.index')
            ->with('success', 'Client supprimé avec succès.');
    }
}