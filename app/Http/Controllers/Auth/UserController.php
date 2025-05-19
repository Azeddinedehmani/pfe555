<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Afficher la liste des utilisateurs.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);
        
        $query = User::query();
        
        // Filtrage par rôle
        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }
        
        // Filtrage par statut
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Recherche par nom ou email
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $users = $query->latest()->paginate(15);
        
        return view('users.index', compact('users'));
    }

    /**
     * Afficher le formulaire de création.
     */
    public function create()
    {
        $this->authorize('create', User::class);
        
        return view('users.create');
    }

    /**
     * Enregistrer un nouvel utilisateur.
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|in:responsable,pharmacien',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);
        
        // Hasher le mot de passe
        $validated['password'] = Hash::make($validated['password']);
        
        // Enregistrer l'image si fournie
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('users', 'public');
        }
        
        $user = User::create($validated);
        
        // Enregistrement de l'activité
        activity_log('create', $user, 'A créé un nouvel utilisateur: ' . $user->name);
        
        return redirect()->route('users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Afficher les détails d'un utilisateur.
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);
        
        $user->load(['activityLogs' => function($query) {
            $query->latest()->take(10);
        }]);
        
        return view('users.show', compact('user'));
    }

    /**
     * Afficher le formulaire de modification.
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        
        return view('users.edit', compact('user'));
    }

    /**
     * Mettre à jour un utilisateur.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => 'required|in:responsable,pharmacien',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
        ]);
        
        // Mettre à jour le mot de passe si fourni
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);
            
            $validated['password'] = Hash::make($request->password);
        }
        
        // Mettre à jour l'image si fournie
        if ($request->hasFile('image')) {
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            $validated['image'] = $request->file('image')->store('users', 'public');
        }
        
        $user->update($validated);
        
        // Enregistrement de l'activité
        activity_log('update', $user, 'A mis à jour l\'utilisateur: ' . $user->name);
        
        return redirect()->route('users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Supprimer un utilisateur.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        
        // Empêcher la suppression de soi-même
        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }
        
        // Empêcher la suppression du dernier responsable
        if ($user->isResponsable() && User::where('role', 'responsable')->count() <= 1) {
            return redirect()->route('users.index')
                ->with('error', 'Impossible de supprimer le dernier utilisateur responsable.');
        }
        
        $userName = $user->name;
        
        // Supprimer l'image si elle existe
        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }
        
        $user->delete();
        
        // Enregistrement de l'activité
        activity_log('delete', $user, 'A supprimé l\'utilisateur: ' . $userName);
        
        return redirect()->route('users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
    
    /**
     * Afficher les journaux d'activité.
     */
    public function activityLogs(Request $request)
    {
        $this->authorize('viewActivityLogs', User::class);
        
        $query = ActivityLog::with('user');
        
        // Filtrage par utilisateur
        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }
        
        // Filtrage par action
        if ($request->has('action') && $request->action != '') {
            $query->where('action', $request->action);
        }
        
        // Filtrage par date
        if ($request->has('date_start') && $request->has('date_end')) {
            $query->whereBetween('created_at', [
                $request->date_start . ' 00:00:00', 
                $request->date_end . ' 23:59:59'
            ]);
        }
        
        $logs = $query->latest()->paginate(25);
        $users = User::all();
        
        return view('users.activity_logs', compact('logs', 'users'));
    }
}