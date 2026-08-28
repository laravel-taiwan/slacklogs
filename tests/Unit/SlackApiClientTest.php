<?php

namespace Tests\Unit;

use App\Services\SlackApiClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SlackApiClientTest extends TestCase
{
    public function test_it_follows_slack_cursor_pagination(): void
    {
        config(['services.slack.token' => 'test-token']);
        Http::fakeSequence()
            ->push(['ok' => true, 'channels' => [['id' => 'C1']], 'response_metadata' => ['next_cursor' => 'next']])
            ->push(['ok' => true, 'channels' => [['id' => 'C2']], 'response_metadata' => ['next_cursor' => '']]);

        $channels = app(SlackApiClient::class)->all('conversations.list', ['limit' => 200], 'channels');

        $this->assertSame([['id' => 'C1'], ['id' => 'C2']], $channels);
        Http::assertSentCount(2);
    }
}
