<?php

namespace App\Services\AI\Drivers;

use App\Contracts\AIProviderDriver;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class GoogleDriver implements AIProviderDriver
{
    protected Client $client;

    public function __construct(protected string $apiKey, protected ?string $baseUrl = null)
    {
        $this->client = new Client([
            'base_uri' => rtrim($this->baseUrl ?: 'https://generativelanguage.googleapis.com/v1beta', '/') . '/',
            'headers' => ['Content-Type' => 'application/json'],
            'timeout' => 60,
        ]);
    }

    public function chat(string $model, array $messages, array $options = []): array
    {
        // Google's Gemini API shape differs from the OpenAI/Anthropic-style
        // messages array — it wants "contents" with role user/model and
        // parts. Normalize the incoming provider-agnostic messages here.
        $contents = [];
        $systemInstruction = null;

        foreach ($messages as $message) {
            if (($message['role'] ?? null) === 'system') {
                $systemInstruction = ['parts' => [['text' => $message['content']]]];

                continue;
            }

            $contents[] = [
                'role' => $message['role'] === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $message['content']]],
            ];
        }

        $payload = array_merge(
            ['contents' => $contents],
            $systemInstruction ? ['systemInstruction' => $systemInstruction] : [],
            ['generationConfig' => $options]
        );

        $response = $this->client->post(
            "models/{$model}:generateContent?key={$this->apiKey}",
            ['json' => $payload]
        );

        $body = json_decode((string) $response->getBody(), true);

        return [
            'content' => $body['candidates'][0]['content']['parts'][0]['text'] ?? '',
            'input_tokens' => $body['usageMetadata']['promptTokenCount'] ?? 0,
            'output_tokens' => $body['usageMetadata']['candidatesTokenCount'] ?? 0,
            'raw' => $body,
        ];
    }

    public function embed(string $model, string|array $input): array
    {
        $texts = is_array($input) ? $input : [$input];

        $response = $this->client->post(
            "models/{$model}:batchEmbedContents?key={$this->apiKey}",
            [
                'json' => [
                    'requests' => array_map(
                        fn (string $text) => ['model' => "models/{$model}", 'content' => ['parts' => [['text' => $text]]]],
                        $texts
                    ),
                ],
            ]
        );

        $body = json_decode((string) $response->getBody(), true);

        return [
            'embeddings' => array_column($body['embeddings'] ?? [], 'values'),
            'input_tokens' => 0, // Gemini's embed endpoint doesn't return token usage.
            'raw' => $body,
        ];
    }

    public function listModels(): array
    {
        try {
            $response = $this->client->get("models?key={$this->apiKey}");
            $body = json_decode((string) $response->getBody(), true);

            return array_map(
                fn (array $model) => ['id' => str_replace('models/', '', $model['name']), 'name' => $model['displayName'] ?? null],
                $body['models'] ?? []
            );
        } catch (GuzzleException) {
            return [];
        }
    }

    public function validateKey(string $apiKey): bool
    {
        try {
            $client = new Client([
                'base_uri' => rtrim($this->baseUrl ?: 'https://generativelanguage.googleapis.com/v1beta', '/') . '/',
                'timeout' => 15,
            ]);

            $response = $client->get("models?key={$apiKey}");

            return $response->getStatusCode() === 200;
        } catch (GuzzleException) {
            return false;
        }
    }
}
