<?php

namespace App\Domain;

use MongoDB\Laravel\Eloquent\Model;

class Channel extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'channels';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'created' => 'integer',
            'is_archived' => 'boolean',
            'is_member' => 'boolean',
            'num_members' => 'integer',
            'members' => 'array',
            'purpose' => 'array',
            'topic' => 'array',
        ];
    }
}
