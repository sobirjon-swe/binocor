<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'amount',
        'due_date',
        'paid_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'paid_date' => 'date',
        ];
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }
}
