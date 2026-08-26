<?php

namespace App\Services\AI\Drivers;

use App\Contracts\AIProviderDriver;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class AnthropicDriver implements AIProviderDriver
{
    protected Client $client;

    protected const ANTHROPIC_VERSION = '2023-06-01';

    public function __construct(protected string $apiKey, protected ?string $baseUrl = null)
    {
        $this->client = new Client([
            'base_uri' => rtrim($this->baseUrl ?: 'https://api.anthropic.com/v1', '/') . '/',
            'headers' => [
                'x-api-key' => $this->apiKey,
                'anthropic-version' => self::ANTHROPIC_VERSION,
                'Content-Type' => 'application/json',
            ],
            'timeout' => 60,
        ]);
    }

    public function chat(string $model, array $messages, array $options = []): array
    {
        // Anthropic requires a top-level "system" param rather than a
        // system-role message inside the messages array — normalize that
        // here so callers can pass messages the same way for every provider.
        $system = null;
        $chatMessages = [];

        foreach ($messages as $message) {
            if (($message['role'] ?? null) === 'system') {
                $system = $message['content'];

                continue;
            }

            $chatMessages[] = $message;
        }

        $payload = array_merge([
            'model' => $model,
            'messages' => $chatMessages,
            'max_tokens' => $options['max_tokens'] ?? 1024,
        ], $system ? ['system' => $system] : [], array_diff_key($options, ['max_tokens' => true]));

        $response = $this->client->post('messages', ['json' => $payload]);
        $body = json_decode((string) $response->getBody(), true);

        return [
            'content' => $body['content'][0]['text'] ?? '',
            'input_tokens' => $body['usage']['input_tokens'] ?? 0,
            'output_tokens' => $body['usage']['output_tokens'] ?? 0,
            'raw' => $body,
        ];
    }

    public function embed(string $model, string|array $input): array
    {
        // Anthropic does not currently expose a public embeddings endpoint.
        // Left explicit and empty (rather than silently faked) so a caller
        // relying on embeddings knows to route that call to a different
        // provider instead.
        throw new \RuntimeException('Anthropic does not provide an embeddings endpoint. Route embedding requests to OpenAI, Google, or OpenRouter instead.');
    }

    public function listModels(): array
    {
        try {
            $response = $this->client->get('models');
            $body = json_decode((string) $response->getBody(), true);

            return array_map(
                fn (array $model) => ['id' => $model['id'], 'name' => $model['display_name'] ?? $model['id']],
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
                'base_uri' => rtrim($this->baseUrl ?: 'https://api.anthropic.com/v1', '/') . '/',
                'headers' => [
                    'x-api-key' => $apiKey,
                    'anthropic-version' => self::ANTHROPIC_VERSION,
                ],
                'timeout' => 15,
            ]);

            $response = $client->get('models');

            return $response->getStatusCode() === 200;
        } catch (GuzzleException) {
            return false;
        }
    }
}
