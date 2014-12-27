<?php

use Frlnc\Slack\Core\Commander;
use Frlnc\Slack\Http\CurlInteractor;
use Frlnc\Slack\Http\SlackResponseFactory;
use Illuminate\Console\Command;

class LoadMessagesCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'slack:load-messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load Slack Messages.';

    /**
     * Create a new command instance.
     *
     * @return void
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
    public function fire()
    {
        //
        $interactor = new CurlInteractor();
        $interactor->setResponseFactory(new SlackResponseFactory());

        $commander = new Commander($_ENV['SLACK_KEY'], $interactor);

        $channels = Channel::where('is_member', true)->get();

        foreach ($channels as $channel) {
            $latest = $channel->latest ?: 0;

            do {
                $response = $commander->execute('channels.history', [
                    'channel' => $channel->sid,
                    'oldest'  => $latest,
                    'count'   => 1000
                ]);

                $responseBody = $response->getBody();

                foreach ($responseBody['messages'] as $msg) {
                    $latest = ($msg['ts'] > $latest) ? $msg['ts'] : $latest;

                    $message = new Message();

                    foreach ($msg as $k => $v) {
                        $message->{$k} = is_string($v) ? $v : (object) $v;
                    }

                    $message->channel = $channel->sid;
                    $message->save();
                }
            } while ($responseBody['has_more']);

            $channel->latest = $latest;
            $channel->save();
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
        ];
    }
}
