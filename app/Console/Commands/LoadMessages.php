<?php

namespace App\Console\Commands;

use App\Channel;
use App\Message;
use Frlnc\Slack\Core\Commander;
use Frlnc\Slack\Http\CurlInteractor;
use Frlnc\Slack\Http\SlackResponseFactory;
use Illuminate\Console\Command;

class LoadMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slack:load-messages';

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

        if (env('SLACK_ARCHIVE_ALL', false)) {
            $channels = Channel::all();
        } else {
            $channels = Channel::getIsMemberChannels();
        }

        foreach ($channels as $channel) {
            $latest = $channel->latest ?: 0;

            do {
                $response = $commander->execute('channels.history', [
                    'channel'   => $channel->sid,
                    'oldest'    => $latest,
                    'count'     => 1000
                ]);

                $responseBody = $response->getBody();

                foreach ($responseBody['messages'] as $message) {
                    $latest = ($message['ts'] > $latest) ? $message['ts'] : $latest;

                    $msg = new Message();

                    foreach ($message as $field => $value) {
                        $msg->{$field} = is_string($value) ? $value : (object) $value;
                    }

                    $msg->channel = $channel->sid;
                    $msg->save();
                }
            } while($responseBody['has_more']);

            $channel->latest = $latest;
            $channel->save();
        }
    }
}
