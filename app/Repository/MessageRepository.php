<?php

namespace App\Repository;

use App\Channel;
use App\Message;
use App\Transformers\LogTransformer;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Serializer\DataArraySerializer;

class MessageRepository
{
    protected $upLimit = 100;

    protected $downLimit = 200;

    private function transform($collection)
    {
        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());
        $resource = new Collection($collection, new LogTransformer());

        return $manager->createData($resource)->toArray();
    }

    public function getAroundDate(Channel $channel, \DateTime $date)
    {
        $timestamp = $date->getTimestamp();

        $messages = Message::where('channel', $channel->sid)
            ->where('ts', '>', $timestamp)
            ->take($this->downLimit)
            ->orderBy('ts', 'asc')->get();

        $previousMessages = Message::where('channel', $channel->sid)
            ->where('ts', '<', $timestamp)
            ->take($this->upLimit)
            ->orderBy('ts', 'desc')->get();

        $previousMessages = $previousMessages->reverse();

        $result = $this->transform($previousMessages->merge($messages));

        $result['meta'] = [
            'moreup'    => ($previousMessages->count() == $this->upLimit),
            'moredown'  => ($messages->count() == $this->downLimit)
        ];

        return $result;
    }

    public function getLatest(Channel $channel)
    {
        $messages = Message::where('channel', $channel->sid)
            ->take($this->downLimit + $this->upLimit)
            ->orderBy('ts', 'desc')->get();

        $result = $this->transform($messages->reverse());

        $result['meta'] = [
            'moreup'    => ($messages->count() == $this->downLimit + $this->upLimit),
            'moredown'  => false
        ];

        return $result;
    }

    public function getTimeLine(Channel $channel)
    {
        $firstMsg = Message::where('channel', $channel->sid)->orderBy('ts', 'asc')->first();
        $lastMsg = Message::where('channel', $channel->sid)->orderBy('ts', 'desc')->first();
        $firstDate = $firstMsg->getCarbon();
        $lastDate = $lastMsg->getCarbon();

        return [
            'first' => $firstDate,
            'last'  => $lastDate
        ];

    }

}