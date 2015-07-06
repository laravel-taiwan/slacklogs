<?php

namespace App;

use Cache;
use Carbon\Carbon;

class Message extends \Moloquent
{
    protected $collection = 'messages';

    public function getCarbon()
    {
        return Carbon::createFromTimestamp($this->ts);
    }

    public function getDate($format = 'Y-m-d')
    {
        return Carbon::createFromTimestamp($this->ts)->format($format);
    }

    public function getUser()
    {
        if ($this->subtype == 'bot_message') { // bot message
            // https://api.slack.com/events/message/bot_message
            return $this->username;
        }

        return User::getName($this->user);
    }
}