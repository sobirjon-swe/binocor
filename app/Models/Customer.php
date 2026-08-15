<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'phone',
        'passport_number',
        'address',
        'lead_status',
    ];

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }
}
