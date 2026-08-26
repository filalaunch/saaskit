<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Platform-level API keys — the founder's own keys, used to serve AI
 * requests for customers who haven't added their own BYOK key. Distinct
 * from UserAiKey, which holds each customer's own bring-your-own-key.
 */
class AiApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'ai_provider_id',
        'label',
        'encrypted_key',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        // Laravel's built-in encrypted cast — uses APP_KEY, encrypts on
        // write and transparently decrypts on read. Never log this
        // attribute's value; always display via getMaskedKeyAttribute().
        'encrypted_key' => 'encrypted',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    protected $hidden = [
        'encrypted_key',
    ];

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
