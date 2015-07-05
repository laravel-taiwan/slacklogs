<?php

namespace App\Console\Commands;

use App\User;
use Frlnc\Slack\Core\Commander;
use Frlnc\Slack\Http\CurlInteractor;
use Frlnc\Slack\Http\SlackResponseFactory;
use Illuminate\Console\Command;
use Mockery\CountValidator\Exception;

class LoadMembers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'slack:load-members';

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

        $response = $commander->execute('users.list');

        $responseBody = $response->getBody();

        if (! $responseBody or ! $responseBody['ok']) {
            throw new Exception('Sth Error Happened');
        }

        foreach ($responseBody['members'] as $member) {
            $userData = [
                'sid'       => $member['id'],
                'name'      => $member['name'],
                'deleted'   => $member['deleted'],
                'status'    => array_get($member, 'status', null),
                'color'     => array_get($member, 'color', null),
                'real_name' => array_get($member, 'real_name', null),
                'profile'   => (object) $member['profile'],
                'is_bot'    => array_get($member, 'is_bot', false),
                'has_files' => array_get($member, 'has_files', false)
            ];

            if ($user = User::where('sid', $member['id'])->first()) {
                foreach ($userData as $field => $value) {
                    $user->{$field} = $value;
                }
                $user->save();
            } else {
                User::create($userData);
            }
        }
    }
}
