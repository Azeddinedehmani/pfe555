<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'status'
    ];

    /**
     * Relation avec les produits.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Déterminer si la catégorie est active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Calculer la valeur totale des produits dans cette catégorie.
     */
    public function getTotalValue()
    {
        return $this->products()->sum(DB::raw('quantity * buy_price'));
    }

    /**
     * Calculer la valeur de vente totale des produits dans cette catégorie.
     */
    public function getTotalSellValue()
    {
        return $this->products()->sum(DB::raw('quantity * sell_price'));
    }

    /**
     * Obtenir le nombre de produits à faible stock dans cette catégorie.
     */
    public function getLowStockCount()
    {
        return $this->products()->whereRaw('quantity <= alert_quantity')->count();
    }
}