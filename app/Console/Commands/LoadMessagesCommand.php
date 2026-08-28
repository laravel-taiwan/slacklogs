<?php

namespace App\Console\Commands;

use App\Domain\Channel;
use App\Domain\Message;
use App\Services\SlackApiClient;
use Illuminate\Console\Command;

class LoadMessagesCommand extends Command
{
    protected $signature = 'slack:load-messages';

    protected $description = 'Import new Slack messages for joined channels';

    public function handle(SlackApiClient $slack): int
    {
        $imported = 0;

        foreach (Channel::query()->where('is_member', true)->get() as $channel) {
            $latest = (string) ($channel->latest ?: '0');
            $messages = $slack->all('conversations.history', [
                'channel' => $channel->sid,
                'oldest' => $latest,
                'inclusive' => false,
                'limit' => 200,
            ], 'messages');

            foreach ($messages as $payload) {
                $timestamp = (string) ($payload['ts'] ?? '');
                if ($timestamp === '') {
                    continue;
                }

                Message::query()->updateOrCreate(
                    ['channel' => $channel->sid, 'ts' => $timestamp],
                    $payload,
                );

                if ((float) $timestamp > (float) $latest) {
                    $latest = $timestamp;
                }

                $imported++;
            }

            $channel->latest = $latest;
            $channel->save();
        }

        $this->info("Imported or refreshed {$imported} Slack messages.");

        return self::SUCCESS;
    }
}
