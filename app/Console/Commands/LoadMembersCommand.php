<?php

namespace App\Console\Commands;

use App\Domain\User;
use App\Services\SlackApiClient;
use Illuminate\Console\Command;

class LoadMembersCommand extends Command
{
    protected $signature = 'slack:load-members';

    protected $description = 'Import Slack member profiles';

    public function handle(SlackApiClient $slack): int
    {
        $members = $slack->all('users.list', ['limit' => 200], 'members');

        foreach ($members as $member) {
            User::query()->updateOrCreate(['sid' => $member['id']], [
                'name' => $member['name'] ?? $member['id'],
                'deleted' => $member['deleted'] ?? false,
                'color' => $member['color'] ?? null,
                'profile' => $member['profile'] ?? [],
            ]);
        }

        $this->info('Imported '.count($members).' Slack members.');

        return self::SUCCESS;
    }
}
