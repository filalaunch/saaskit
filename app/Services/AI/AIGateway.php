<?php

namespace App\Services\AI;

use App\Contracts\AIProviderDriver;
use App\Models\AiApiKey;
use App\Models\AiModel;
use App\Models\AiUsageLog;
use App\Models\User;
use App\Models\UserAiKey;
use App\Services\AI\Drivers\AnthropicDriver;
use App\Services\AI\Drivers\GoogleDriver;
use App\Services\AI\Drivers\OpenAIDriver;
use App\Services\AI\Drivers\OpenRouterDriver;
use RuntimeException;

class AIGateway
{
    /**
     * Maps an AiProvider's slug to the driver class that implements it.
     * Add a new provider by adding one line here plus its driver class —
     * nothing else in this gateway needs to change.
     *
     * @var array<string, class-string<AIProviderDriver>>
     */
    protected array $driverMap = [
        'openai' => OpenAIDriver::class,
        'anthropic' => AnthropicDriver::class,
        'google' => GoogleDriver::class,
        'openrouter' => OpenRouterDriver::class,
    ];

    /**
     * Send a chat request on behalf of a user, using their BYOK key if
     * they have one active for this model's provider, otherwise the
     * platform's default key for that provider. Every call is logged to
     * ai_usage_logs regardless of which key was used.
     *
     * @param  array<int, array{role: string, content: string}>  $messages
     * @param  array<string, mixed>  $options  Pass 'feature_context' => 'blog_content_assist' etc. to tag the log entry.
     */
    public function chat(User $user, AiModel $model, array $messages, array $options = []): array
    {
        $featureContext = $options['feature_context'] ?? null;
        unset($options['feature_context']);

        [$driver, $source] = $this->resolveDriver($user, $model);

        $result = $driver->chat($model->model_key, $messages, $options);

        AiUsageLog::create([
            'user_id' => $user->id,
            'ai_model_id' => $model->id,
            'source' => $source,
            'input_tokens' => $result['input_tokens'] ?? 0,
            'output_tokens' => $result['output_tokens'] ?? 0,
            // Only the platform's own key usage counts as a cost to the
            // founder — BYOK usage is billed directly by the provider to
            // the customer, so it's logged with no computed_cost.
            'computed_cost' => $source === 'platform_key'
                ? $this->computeCost($model, $result)
                : null,
            'feature_context' => $featureContext,
        ]);

        return $result;
    }

    /**
     * Resolve which driver + key source to use for a given user/model pair.
     *
     * @return array{0: AIProviderDriver, 1: string} [driver instance, 'byok'|'platform_key']
     */
    protected function resolveDriver(User $user, AiModel $model): array
    {
        $provider = $model->provider;

        if (! $provider) {
            throw new RuntimeException("AI model [{$model->id}] has no associated provider.");
        }

        $driverClass = $this->driverMap[$provider->slug] ?? null;

        if (! $driverClass) {
            throw new RuntimeException("No driver registered for AI provider [{$provider->slug}]. Add one to AIGateway::\$driverMap.");
        }

        $userKey = UserAiKey::query()
            ->where('user_id', $user->id)
            ->where('ai_provider_id', $provider->id)
            ->where('is_active', true)
            ->first();

        if ($userKey) {
            return [new $driverClass($userKey->encrypted_key, $provider->base_url), 'byok'];
        }

        $platformKey = AiApiKey::query()
            ->where('ai_provider_id', $provider->id)
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->first();

        if (! $platformKey) {
            throw new RuntimeException(
                "No usable API key for provider [{$provider->name}]. Add a platform key in Admin > AI Management, or ask the customer to add their own BYOK key."
            );
        }

        return [new $driverClass($platformKey->encrypted_key, $provider->base_url), 'platform_key'];
    }

    /**
     * Compute the cost of a completed request in minor units (cents),
     * based on the model's configured per-1k-token pricing. Only called
     * when the platform's own key was used, since BYOK usage costs the
     * founder nothing directly.
     */
    protected function computeCost(AiModel $model, array $result): int
    {
        $inputCost = ((float) ($result['input_tokens'] ?? 0) / 1000) * (float) $model->input_price_per_1k;
        $outputCost = ((float) ($result['output_tokens'] ?? 0) / 1000) * (float) $model->output_price_per_1k;

        return (int) round($inputCost + $outputCost);
    }
}
