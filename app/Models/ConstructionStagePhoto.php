<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ConstructionStagePhoto extends Model
{
    protected $fillable = [
        'construction_stage_id',
        'user_id',
        'path',
        'note',
    ];

    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn () => Storage::disk('public')->url($this->path),
        );
    }

    public function constructionStage(): BelongsTo
    {
        return $this->belongsTo(ConstructionStage::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
