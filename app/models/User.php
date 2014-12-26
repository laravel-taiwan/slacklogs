<?php

class User extends Moloquent
{
    protected $collection = 'users';

    protected $fillable = ['sid', 'name', 'deleted', 'color' ,'profile'];
}
