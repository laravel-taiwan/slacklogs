<?php

namespace App\Console\Commands;

use App\Domain\Channel;
use App\Services\SlackApiClient;
use Illuminate\Console\Command;

class LoadChannelsCommand extends Command
{
    protected $signature = 'slack:load-channels';

    protected $description = 'Import Slack channel metadata';

    public function handle(SlackApiClient $slack): int
    {
        $channels = $slack->all('conversations.list', [
            'types' => 'public_channel,private_channel',
            'exclude_archived' => false,
            'limit' => 200,
        ], 'channels');

        foreach ($channels as $channel) {
            $record = Channel::query()->firstOrNew(['sid' => $channel['id']]);
            $record->fill([
                'name' => $channel['name'],
                'created' => $channel['created'] ?? null,
                'creator' => $channel['creator'] ?? null,
                'purpose' => $channel['purpose'] ?? [],
                'topic' => $channel['topic'] ?? [],
                'is_archived' => $channel['is_archived'] ?? false,
                'is_member' => $channel['is_member'] ?? false,
                'num_members' => $channel['num_members'] ?? count($channel['members'] ?? []),
                'members' => $channel['members'] ?? [],
            ]);
            $record->latest ??= '0';
            $record->save();
        }

        $this->info('Imported '.count($channels).' Slack channels.');

        return self::SUCCESS;
    }
}
