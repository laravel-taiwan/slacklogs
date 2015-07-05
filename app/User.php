<?php

namespace App;

class User extends \Moloquent
{
    protected $collection = 'users';

    protected $fillable = [
        'sid', 'name', 'status', 'deleted', 'color', 'real_name',
        'profile', 'is_bot', 'has_files'
    ];
}
