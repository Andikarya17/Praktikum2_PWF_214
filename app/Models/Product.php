<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'qty',
        'price',
        'user_id',
    ];

    /**
     * Accessor: supaya bisa pakai $product->quantity di view
     * padahal kolom di database adalah 'qty'
     */
    public function getQuantityAttribute(): int
    {
        return $this->qty;
    }

    /**
     * Relasi: Product dimiliki oleh satu User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
