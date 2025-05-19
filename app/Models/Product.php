<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'category_id',
        'barcode',
        'buy_price',
        'sell_price',
        'quantity',
        'alert_quantity',
        'expiry_date',
        'manufacturing_date',
        'location',
        'image',
        'status'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'manufacturing_date' => 'date',
    ];

    // Relations
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    // Scopes
    public function scopeLowStock($query)
    {
        return $query->whereRaw('quantity <= alert_quantity');
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', now())
            ->whereDate('expiry_date', '<=', now()->addDays($days));
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', now());
    }

    // Méthodes
    public function isLowStock()
    {
        return $this->quantity <= $this->alert_quantity;
    }

    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiringSoon($days = 30)
    {
        return $this->expiry_date 
            && $this->expiry_date->isFuture() 
            && $this->expiry_date->diffInDays(now()) <= $days;
    }
}