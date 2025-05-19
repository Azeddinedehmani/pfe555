<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'status'
    ];

    /**
     * Relation avec les ventes.
     */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Relation avec les ordonnances.
     */
    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    /**
     * Déterminer si le client est actif.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Calculer le total dépensé par le client.
     */
    public function getTotalSpent()
    {
        return $this->sales()->sum('final_amount');
    }

    /**
     * Obtenir le nombre de visites (ventes) du client.
     */
    public function getVisitCount()
    {
        return $this->sales()->count();
    }

    /**
     * Obtenir la date de la dernière visite du client.
     */
    public function getLastVisitDate()
    {
        $lastSale = $this->sales()->latest()->first();
        
        return $lastSale ? $lastSale->created_at : null;
    }
}