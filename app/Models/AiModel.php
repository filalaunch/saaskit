<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiModel extends Model
{
    use HasFactory;

    protected $table = 'ai_models';

    protected $fillable = [
        'ai_provider_id',
        'name',
        'model_key',
        'input_price_per_1k',
        'output_price_per_1k',
        'context_window',
        'capabilities',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'input_price_per_1k' => 'decimal:6',
        'output_price_per_1k' => 'decimal:6',
        'context_window' => 'integer',
        'capabilities' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function provider(): BelongsTo
    {
        return $this->belongsTo(AiProvider::class, 'ai_provider_id');
    }

    public function usageLogs(): HasMany
    {
        return $this->hasMany(AiUsageLog::class, 'ai_model_id');
    }
}
