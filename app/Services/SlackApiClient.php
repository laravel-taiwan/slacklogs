<?php

namespace App\Services;

use Illuminate\Http\Client\Factory;
use RuntimeException;

class SlackApiClient
{
    public function __construct(private readonly Factory $http)
    {
    }

    /** @return array<int, array<string, mixed>> */
    public function all(string $method, array $parameters, string $resultKey): array
    {
        $token = (string) config('services.slack.token');
        if ($token === '') {
            throw new RuntimeException('SLACK_BOT_TOKEN is not configured.');
        }

        $items = [];
        $cursor = null;

        do {
            $response = $this->http
                ->withToken($token)
                ->acceptJson()
                ->baseUrl('https://slack.com/api')
                ->retry(3, 200)
                ->get($method, array_filter($parameters + ['cursor' => $cursor], static fn (mixed $value): bool => $value !== null && $value !== ''))
                ->throw();

            $payload = $response->json();
            if (! is_array($payload) || ($payload['ok'] ?? false) !== true) {
                throw new RuntimeException('Slack API error: '.($payload['error'] ?? 'invalid response'));
            }

            $page = $payload[$resultKey] ?? null;
            if (! is_array($page)) {
                throw new RuntimeException("Slack API response did not include {$resultKey}.");
            }

            array_push($items, ...$page);
            $cursor = data_get($payload, 'response_metadata.next_cursor') ?: null;
        } while ($cursor !== null);

        return $items;
    }
}
