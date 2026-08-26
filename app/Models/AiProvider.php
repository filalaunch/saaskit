<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'base_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function models(): HasMany
    {
        return $this->hasMany(AiModel::class);
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(AiApiKey::class);
    }

    public function userKeys(): HasMany
    {
        return $this->hasMany(UserAiKey::class);
    }
}
