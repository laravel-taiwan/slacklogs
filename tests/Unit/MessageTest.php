<?php

namespace Tests\Unit;

use App\Domain\Message;
use Tests\TestCase;

class MessageTest extends TestCase
{
    public function test_it_builds_a_stable_archive_url_from_a_slack_timestamp(): void
    {
        config(['app.timezone' => 'Asia/Taipei']);

        $message = new Message(['ts' => '1724803200.123456']);
        $message->setAttribute('_id', 'message-id');

        $this->assertSame('2024-08-28/08:00:00#log-message-id', $message->getUrl());
    }
}
