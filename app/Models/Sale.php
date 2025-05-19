<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'user_id',
        'prescription_id',
        'reference',
        'total_amount',
        'discount',
        'tax',
        'final_amount',
        'payment_method',
        'payment_status',
        'notes'
    ];

    // Relations
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    public function scopePartial($query)
    {
        return $query->where('payment_status', 'partial');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', now());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    // Méthodes
    public static function generateReference()
    {
        $latest = self::latest()->first();
        $prefix = 'INV-';
        $year = date('Y');
        $month = date('m');
        
        if (!$latest) {
            return $prefix . $year . $month . '0001';
        }
        
        $string = preg_replace('/[^0-9]/', '', $latest->reference);
        $number = intval($string) + 1;
        
        return $prefix . $year . $month . sprintf('%04d', $number);
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function getFormattedTotal()
    {
        return number_format($this->final_amount, 2) . ' DH';
    }
}