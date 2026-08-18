<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Contract extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'user_id',
        'customer_id',
        'property_id',
        'total_price',
        'payment_type',
        'down_payment',
        'installment_months',
        'signed_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'signed_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->orderBy('due_date');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['total_price', 'status'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}
