<?php

namespace Database\Seeders;

use App\Models\AiModel;
use App\Models\AiProvider;
use Illuminate\Database\Seeder;

/**
 * Seeds the 4 confirmed out-of-the-box providers (build spec §4.11) plus a
 * handful of example models for each.
 *
 * IMPORTANT: the model names/prices below are illustrative starting points,
 * not guaranteed-current data — every AI provider changes their model
 * lineup and pricing frequently. Verify against each provider's own
 * pricing page and update these rows (or just edit them from
 * Admin > AI Management > Models) before relying on cost calculations.
 */
class AiProviderSeeder extends Seeder
{
    public function run(): void
    {
        $openai = AiProvider::updateOrCreate(
            ['slug' => 'openai'],
            ['name' => 'OpenAI', 'is_active' => true]
        );

        AiModel::updateOrCreate(
            ['ai_provider_id' => $openai->id, 'model_key' => 'gpt-4.1'],
            [
                'name' => 'GPT-4.1',
                'input_price_per_1k' => 0.002,
                'output_price_per_1k' => 0.008,
                'context_window' => 1000000,
                'capabilities' => ['text', 'vision', 'tools'],
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        AiModel::updateOrCreate(
            ['ai_provider_id' => $openai->id, 'model_key' => 'gpt-4.1-mini'],
            [
                'name' => 'GPT-4.1 Mini',
                'input_price_per_1k' => 0.0004,
                'output_price_per_1k' => 0.0016,
                'context_window' => 1000000,
                'capabilities' => ['text', 'vision', 'tools'],
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $anthropic = AiProvider::updateOrCreate(
            ['slug' => 'anthropic'],
            ['name' => 'Anthropic', 'is_active' => true]
        );

        AiModel::updateOrCreate(
            ['ai_provider_id' => $anthropic->id, 'model_key' => 'claude-sonnet-5'],
            [
                'name' => 'Claude Sonnet 5',
                'input_price_per_1k' => 0.003,
                'output_price_per_1k' => 0.015,
                'context_window' => 200000,
                'capabilities' => ['text', 'vision', 'tools'],
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        AiModel::updateOrCreate(
            ['ai_provider_id' => $anthropic->id, 'model_key' => 'claude-haiku-4-5-20251001'],
            [
                'name' => 'Claude Haiku 4.5',
                'input_price_per_1k' => 0.0008,
                'output_price_per_1k' => 0.004,
                'context_window' => 200000,
                'capabilities' => ['text', 'vision', 'tools'],
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $google = AiProvider::updateOrCreate(
            ['slug' => 'google'],
            ['name' => 'Google', 'is_active' => true]
        );

        AiModel::updateOrCreate(
            ['ai_provider_id' => $google->id, 'model_key' => 'gemini-2.5-pro'],
            [
                'name' => 'Gemini 2.5 Pro',
                'input_price_per_1k' => 0.00125,
                'output_price_per_1k' => 0.005,
                'context_window' => 1000000,
                'capabilities' => ['text', 'vision', 'tools'],
                'is_active' => true,
                'sort_order' => 1,
            ]
        );

        AiModel::updateOrCreate(
            ['ai_provider_id' => $google->id, 'model_key' => 'gemini-2.5-flash'],
            [
                'name' => 'Gemini 2.5 Flash',
                'input_price_per_1k' => 0.000075,
                'output_price_per_1k' => 0.0003,
                'context_window' => 1000000,
                'capabilities' => ['text', 'vision', 'tools'],
                'is_active' => true,
                'sort_order' => 2,
            ]
        );

        $openrouter = AiProvider::updateOrCreate(
            ['slug' => 'openrouter'],
            ['name' => 'OpenRouter', 'is_active' => true]
        );

        AiModel::updateOrCreate(
            ['ai_provider_id' => $openrouter->id, 'model_key' => 'meta-llama/llama-3.1-70b-instruct'],
            [
                'name' => 'Llama 3.1 70B (via OpenRouter)',
                'input_price_per_1k' => 0.0004,
                'output_price_per_1k' => 0.0004,
                'context_window' => 128000,
                'capabilities' => ['text', 'tools'],
                'is_active' => true,
                'sort_order' => 1,
            ]
        );
    }
}
