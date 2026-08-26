<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * BYOK — a customer's own API key for a given provider. When a valid,
 * active key exists here for a user+provider pair, AIGateway prefers it
 * over the platform key, so that customer's usage is billed to their own
 * account with the provider, not to the founder's platform key.
 */
class UserAiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ai_provider_id',
        'label',
        'encrypted_key',
        'is_active',
        'last_validated_at',
    ];

    protected $casts = [
        'encrypted_key' => 'encrypted',
        'is_active' => 'boolean',
        'last_validated_at' => 'datetime',
    ];

    protected $hidden = [
        'encrypted_key',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(AiProvider::class, 'ai_provider_id');
    }

    public function getMaskedKeyAttribute(): string
    {
        $key = $this->encrypted_key;

        if (blank($key)) {
            return '—';
        }

        return substr($key, 0, 3) . str_repeat('•', 8) . substr($key, -4);
    }
}
