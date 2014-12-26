<?php

class Channel extends Moloquent
{
    protected $collection = 'channels';

    protected $fillable = ['sid', 'name', 'created', 'creator', 'is_archived', 'number_members', 'topic', 'members', 'is_member', 'purpose', 'latest'];
}