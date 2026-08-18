<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Property extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'project_id',
        'type',
        'area',
        'floor',
        'rooms_count',
        'price',
        'status',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PropertyPhoto::class)->orderByDesc('is_primary')->latest();
    }

    public function primaryPhoto(): HasOne
    {
        return $this->photos()->one();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['price', 'status'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }
}
