<?php

namespace App\Console\Commands;

use App\Channel;
use Frlnc\Slack\Core\Commander;
use Frlnc\Slack\Http\CurlInteractor;
use Frlnc\Slack\Http\SlackResponseFactory;
use Illuminate\Console\Command;
use Mockery\CountValidator\Exception;

class LoadChannels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slack:load-channels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $interactor = new CurlInteractor();
        $interactor->setResponseFactory(new SlackResponseFactory());

        $commander = new Commander(env('SLACK_KEY'), $interactor);

        $response = $commander->execute('channels.list');

        $responseBody = $response->getBody();

        if (! $responseBody or ! $responseBody['ok']) {
            throw new Exception('Sth Error Happened!');
        }

        foreach ($responseBody['channels'] as $chan) {
            if (! $chan['is_channel']) {
                continue;
            }

            $chanData = [
                'sid'           => $chan['id'],
                'name'          => $chan['name'],
                'created'       => $chan['created'],
                'creator'       => $chan['creator'],
                'purpose'       => (object) $chan['purpose'],
                'is_archived'   => $chan['is_archived'],
                'is_general'    => $chan['is_general'],
                'is_member'     => $chan['is_member'],
                'members'       => $chan['members'],
                'topic'         => (object) $chan['topic'],
                'num_members'   => (int) $chan['num_members']
            ];

            if ($channel = Channel::where('sid', $chan['id'])->first()) {
                foreach ($chanData as $field => $value) {
                    $channel->{$field} = $value;
                }
                $channel->save();
            } else {
                $chanData['latest'] = 0;

                Channel::create($chanData);
            }
        }

    }
}
