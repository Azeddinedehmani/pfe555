<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Afficher la liste des catégories.
     */
    public function index(Request $request)
    {
        $query = Category::query();
        
        // Filtrage par statut
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Recherche par nom
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }
        
        $categories = $query->withCount('products')->latest()->paginate(15);
        
        return view('categories.index', compact('categories'));
    }

    /**
     * Afficher le formulaire de création.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Enregistrer une nouvelle catégorie.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
        ]);
        
        $category = Category::create($validated);
        
        // Enregistrement de l'activité
        activity_log('create', $category, 'A créé une nouvelle catégorie: ' . $category->name);
        
        return redirect()->route('categories.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    /**
     * Afficher le formulaire de modification.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Mettre à jour une catégorie.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories')->ignore($category->id),
            ],
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
        ]);
        
        $category->update($validated);
        
        // Enregistrement de l'activité
        activity_log('update', $category, 'A mis à jour la catégorie: ' . $category->name);
        
        return redirect()->route('categories.index')
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    /**
     * Supprimer une catégorie.
     */
    public function destroy(Category $category)
    {
        // Vérifier si la catégorie a des produits
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Cette catégorie ne peut pas être supprimée car elle contient des produits.');
        }
        
        $categoryName = $category->name;
        $category->delete();
        
        // Enregistrement de l'activité
        activity_log('delete', $category, 'A supprimé la catégorie: ' . $categoryName);
        
        return redirect()->route('categories.index')
            ->with('success', 'Catégorie supprimée avec succès.');
    }
}