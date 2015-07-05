<?php

namespace App;

class Channel extends \Moloquent
{
    protected $connection = 'mongodb';

    protected $fillable = [
        'sid', 'name', 'created', 'creator', 'is_archived', 'is_general',
        'num_members', 'topic', 'members', 'is_member', 'purpose', 'latest'
    ];

    public function scopeIsMember($query)
    {
        return $query->where('is_member', true);
    }

    public static function getIsMemberChannels()
    {
        return static::isMember()->get();
    }

}