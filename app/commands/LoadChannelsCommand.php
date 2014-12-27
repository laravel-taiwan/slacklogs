<?php

use Frlnc\Slack\Core\Commander;
use Frlnc\Slack\Http\CurlInteractor;
use Frlnc\Slack\Http\SlackResponseFactory;
use Illuminate\Console\Command;

class LoadChannelsCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'slack:load-channels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load Channel Lists.';

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
                'sid'         => $chan['id'],
                'name'        => $chan['name'],
                'created'     => $chan['created'],
                'creator'     => $chan['creator'],
                'purpose'     => (object) $chan['purpose'],
                'is_archived' => $chan['is_archived'],
                'is_member'   => $chan['is_member'],
                'num_members' => $chan['num_members'],
                'members'     => $chan['members'],
                'topic'       => (object) $chan['topic'],
            ];

            if ($channel = Channel::where('sid', $chan['id'])->first()) {
                foreach ($chanData as $k => $v) {
                    $channel->{$k} = $v;
                }
                $channel->save();
            } else {
                $chanData['latest'] = 0;

                Channel::create($chanData);
            }
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
