<?php

namespace App\Domain;

use Carbon\CarbonImmutable;
use MongoDB\Laravel\Eloquent\Model;

class Message extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'messages';

    protected $guarded = [];

    public function getUser(): string
    {
        if ($this->subtype === 'bot_message') {
            return (string) ($this->username ?: 'bot');
        }

        if ($this->user === 'USLACKBOT') {
            return 'slackbot';
        }

        return (string) (User::query()->where('sid', $this->user)->value('name') ?: $this->username ?: $this->user ?: 'unknown');
    }

    public function getCarbon(): CarbonImmutable
    {
        return CarbonImmutable::createFromTimestamp((int) floor((float) $this->ts), config('app.timezone'));
    }

    public function getHour(): string
    {
        return $this->getCarbon()->format('H:i');
    }

    public function getDay(): string
    {
        return $this->getCarbon()->format('l, d M');
    }

    public function getUrl(): string
    {
        return $this->getCarbon()->format('Y-m-d/H:i:s').'#log-'.$this->getKey();
    }
}
