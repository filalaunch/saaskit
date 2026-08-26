<?php

namespace App\Services\AI\Drivers;

use App\Contracts\AIProviderDriver;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * OpenRouter proxies dozens of underlying models (GPT, Claude, Gemini,
 * Llama, and more) behind a single OpenAI-compatible API — this is what
 * lets FilaLaunch offer broad multi-model access on day one without a
 * dedicated driver per niche provider. The model_key for an OpenRouter
 * AiModel row should be OpenRouter's own identifier, e.g.
 * "anthropic/claude-3.5-sonnet" or "meta-llama/llama-3.1-70b-instruct".
 */
class OpenRouterDriver implements AIProviderDriver
{
    protected Client $client;

    public function __construct(protected string $apiKey, protected ?string $baseUrl = null)
    {
        $this->client = new Client([
            'base_uri' => rtrim($this->baseUrl ?: 'https://openrouter.ai/api/v1', '/') . '/',
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
                // OpenRouter uses these two headers for their public rankings;
                // harmless to omit, but good practice to identify the app.
                'HTTP-Referer' => config('app.url'),
                'X-Title' => config('app.name'),
            ],
            'timeout' => 60,
        ]);
    }

    public function chat(string $model, array $messages, array $options = []): array
    {
        $response = $this->client->post('chat/completions', [
            'json' => array_merge([
                'model' => $model,
                'messages' => $messages,
            ], $options),
        ]);

        $body = json_decode((string) $response->getBody(), true);

        return [
            'content' => $body['choices'][0]['message']['content'] ?? '',
            'input_tokens' => $body['usage']['prompt_tokens'] ?? 0,
            'output_tokens' => $body['usage']['completion_tokens'] ?? 0,
            'raw' => $body,
        ];
    }

    public function embed(string $model, string|array $input): array
    {
        // OpenRouter's embeddings support varies by underlying model and is
        // not universally available — left unimplemented deliberately
        // rather than silently returning an empty/fake result.
        throw new \RuntimeException('Embeddings are not universally supported across OpenRouter models. Check the specific model\'s capabilities before routing embedding requests here.');
    }

    public function listModels(): array
    {
        try {
            $response = $this->client->get('models');
            $body = json_decode((string) $response->getBody(), true);

            return array_map(
                fn (array $model) => ['id' => $model['id'], 'name' => $model['name'] ?? $model['id']],
                $body['data'] ?? []
            );
        } catch (GuzzleException) {
            return [];
        }
    }

    public function validateKey(string $apiKey): bool
    {
        try {
            $client = new Client([
                'base_uri' => rtrim($this->baseUrl ?: 'https://openrouter.ai/api/v1', '/') . '/',
                'headers' => ['Authorization' => "Bearer {$apiKey}"],
                'timeout' => 15,
            ]);

            $response = $client->get('models');

            return $response->getStatusCode() === 200;
        } catch (GuzzleException) {
            return false;
        }
    }
}
