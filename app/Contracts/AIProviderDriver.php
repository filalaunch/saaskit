<?php

namespace App\Contracts;

/**
 * Every AI provider (OpenAI, Anthropic, Google, OpenRouter, or a future one)
 * implements this exact shape. The AIGateway only ever talks to this
 * interface, never to a provider-specific SDK — that's what keeps the
 * whole module provider-agnostic and makes adding a 5th provider later a
 * matter of writing one new class, not touching the gateway.
 */
interface AIProviderDriver
{
    /**
     * Send a chat/completion request.
     *
     * @param  string  $model  The provider's own model identifier (e.g. "gpt-4.1", "claude-sonnet-5").
     * @param  array<int, array{role: string, content: string}>  $messages
     * @param  array<string, mixed>  $options  Provider-agnostic options (e.g. ['temperature' => 0.7, 'max_tokens' => 1024]).
     * @return array{content: string, input_tokens: int, output_tokens: int, raw: array}
     */
    public function chat(string $model, array $messages, array $options = []): array;

    /**
     * Generate an embedding vector for the given input.
     *
     * @param  string|array<int, string>  $input
     * @return array{embeddings: array, input_tokens: int, raw: array}
     */
    public function embed(string $model, string|array $input): array;

    /**
     * List models currently available from this provider's API, if the
     * provider exposes a models-list endpoint. Not all providers do —
     * implementations that can't support this should return an empty array
     * rather than throwing.
     *
     * @return array<int, array{id: string, name: ?string}>
     */
    public function listModels(): array;

    /**
     * Validate that an API key is genuinely usable (used when a customer
     * adds/tests a BYOK key). Should make the cheapest possible real request
     * against the provider and return whether it succeeded.
     */
    public function validateKey(string $apiKey): bool;
}
