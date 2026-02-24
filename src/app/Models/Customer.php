<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'document',
        'address',
        'city',
        'state',
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function totalPurchased(): float
    {
        return (float) $this->sales()->where('status', 'completed')->sum('total');
    }
}
