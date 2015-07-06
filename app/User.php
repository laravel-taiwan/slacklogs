<?php

namespace App;

use Cache;

class User extends \Moloquent
{
    protected $collection = 'users';

    protected $fillable = [
        'sid', 'name', 'status', 'deleted', 'color', 'real_name',
        'profile', 'is_bot', 'has_files'
    ];

    public static function getName($sid)
    {
        if ($sid === 'USLACKBOT') {
            return 'slackbot';
        }

        $cacheKey = 'user_' . $sid;

        return Cache::remember($cacheKey, 30, function() use ($sid) {
            $user = static::where('sid', $sid)->firstOrFail();

            return data_get($user, 'name', 'UNKNOWN?');
        });
    }
}
