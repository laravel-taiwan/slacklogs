<?php

namespace App\Domain;

use MongoDB\Laravel\Eloquent\Model;

class User extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'users';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'deleted' => 'boolean',
            'profile' => 'array',
        ];
    }
}
