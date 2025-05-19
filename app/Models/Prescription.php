<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prescription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'doctor_name',
        'prescription_date',
        'expiry_date',
        'image',
        'notes',
        'status'
    ];

    protected $casts = [
        'prescription_date' => 'date',
        'expiry_date' => 'date',
    ];

    // Relations
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', now())
            ->whereDate('expiry_date', '<=', now()->addDays($days));
    }

    // Méthodes
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getFormattedDate()
    {
        return $this->prescription_date ? $this->prescription_date->format('d/m/Y') : '';
    }

    public function getFormattedExpiryDate()
    {
        return $this->expiry_date ? $this->expiry_date->format('d/m/Y') : '';
    }
}