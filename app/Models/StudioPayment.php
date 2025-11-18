<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudioPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'amount',
        'payment_method',
        'reference',
        'payment_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime',
    ];

    /**
     * Get session relationship
     */
    public function session()
    {
        return $this->belongsTo(StudioSession::class, 'session_id');
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (!$payment->payment_date) {
                $payment->payment_date = now();
            }
            if (!$payment->reference) {
                $payment->reference = 'PAY-' . strtoupper(uniqid());
            }
        });
    }
}