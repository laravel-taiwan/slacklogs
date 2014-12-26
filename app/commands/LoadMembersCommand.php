<?php

use Frlnc\Slack\Core\Commander;
use Frlnc\Slack\Http\CurlInteractor;
use Frlnc\Slack\Http\SlackResponseFactory;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class LoadMembersCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'slack:load-members';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load Group Members Info.';

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
        $interactor = new CurlInteractor;
        $interactor->setResponseFactory(new SlackResponseFactory);

        $commander = new Commander('xoxp-3246222755-3289345650-3292375155-6438ef', $interactor);

        $response = $commander->execute('users.list');

        $responseBody = $response->getBody();

        if (! $responseBody or ! $responseBody['ok'])
            throw new Exception('Sth Error Happened!');

        foreach($responseBody['members'] as $member) {
            $userData = [
                'sid'        => $member['id'],
                'name'      => $member['name'],
                'deleted'   => $member['deleted'],
                'color'     => $member['color'],
                'profile'   => (object) $member['profile'],
            ];

            if ($user = User::where('sid', $member['id'])->first()) {

                foreach($userData as $k => $v) {
                    $user->{$k} = $v;
                }

                $user->save();
            } else {
                User::create($userData);
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
        return array(
        );
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
        );
    }

}
