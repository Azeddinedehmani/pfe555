<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'details',
        'ip_address',
        'user_agent'
    ];

    /**
     * Relation avec l'utilisateur.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Récupérer le modèle associé à cette activité.
     */
    public function subject()
    {
        if (!$this->model_type || !$this->model_id) {
            return null;
        }

        return app($this->model_type)->find($this->model_id);
    }

    /**
     * Formater l'action pour l'affichage.
     */
    public function getFormattedAction()
    {
        $actions = [
            'create' => 'a créé',
            'update' => 'a modifié',
            'delete' => 'a supprimé',
            'view' => 'a consulté',
            'login' => 's\'est connecté',
            'logout' => 's\'est déconnecté'
        ];

        return $actions[$this->action] ?? $this->action;
    }

    /**
     * Formater le type de modèle pour l'affichage.
     */
    public function getFormattedModelType()
    {
        $types = [
            'App\\Models\\Product' => 'le produit',
            'App\\Models\\Category' => 'la catégorie',
            'App\\Models\\Client' => 'le client',
            'App\\Models\\Sale' => 'la vente',
            'App\\Models\\Purchase' => 'l\'achat',
            'App\\Models\\Supplier' => 'le fournisseur',
            'App\\Models\\Prescription' => 'l\'ordonnance',
            'App\\Models\\User' => 'l\'utilisateur',
            'App\\Models\\Setting' => 'les paramètres'
        ];

        return $types[$this->model_type] ?? $this->model_type;
    }

    /**
     * Formater le message d'activité complet.
     */
    public function getFormattedMessage()
    {
        if ($this->details) {
            return $this->details;
        }

        $message = $this->getFormattedAction();

        if ($this->model_type) {
            $message .= ' ' . $this->getFormattedModelType();
        }

        return $message;
    }
}