<?php

namespace App\Services\AI\Drivers;

use App\Contracts\AIProviderDriver;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class OpenAIDriver implements AIProviderDriver
{
    protected Client $client;

    public function __construct(protected string $apiKey, protected ?string $baseUrl = null)
    {
        $this->client = new Client([
            'base_uri' => rtrim($this->baseUrl ?: 'https://api.openai.com/v1', '/') . '/',
            'headers' => [
                'Authorization' => "Bearer {$this->apiKey}",
                'Content-Type' => 'application/json',
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
        $response = $this->client->post('embeddings', [
            'json' => [
                'model' => $model,
                'input' => $input,
            ],
        ]);

        $body = json_decode((string) $response->getBody(), true);

        return [
            'embeddings' => array_column($body['data'] ?? [], 'embedding'),
            'input_tokens' => $body['usage']['prompt_tokens'] ?? 0,
            'raw' => $body,
        ];
    }

    public function listModels(): array
    {
        try {
            $response = $this->client->get('models');
            $body = json_decode((string) $response->getBody(), true);

            return array_map(
                fn (array $model) => ['id' => $model['id'], 'name' => $model['id']],
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
                'base_uri' => rtrim($this->baseUrl ?: 'https://api.openai.com/v1', '/') . '/',
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
