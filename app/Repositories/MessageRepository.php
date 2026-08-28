<?php

namespace App\Repositories;

use App\Domain\Channel;
use App\Domain\Message;
use Carbon\CarbonImmutable;
use DateTimeInterface;
use MongoDB\BSON\Regex;

class MessageRepository
{
    private const UP_LIMIT = 100;

    private const DOWN_LIMIT = 200;

    /** @return array{firstLog: ?Message, logs: array<int, Message>, moreup: ?string, moredown: null} */
    public function latest(Channel $channel): array
    {
        $logs = Message::query()
            ->where('channel', $channel->sid)
            ->orderByDesc('ts')
            ->limit(self::UP_LIMIT + self::DOWN_LIMIT)
            ->get()
            ->reverse()
            ->values()
            ->all();

        return [
            'firstLog' => $logs === [] ? null : end($logs),
            'logs' => $logs,
            'moreup' => count($logs) === self::UP_LIMIT + self::DOWN_LIMIT ? (string) reset($logs)->getKey() : null,
            'moredown' => null,
        ];
    }

    /** @return array{firstLog: ?Message, logs: array<int, Message>, moreup: ?string, moredown: ?string} */
    public function aroundDate(Channel $channel, DateTimeInterface $date): array
    {
        $timestamp = (string) $date->getTimestamp();
        $after = Message::query()
            ->where('channel', $channel->sid)
            ->where('ts', '>', $timestamp)
            ->orderBy('ts')
            ->limit(self::DOWN_LIMIT)
            ->get()
            ->all();
        $before = Message::query()
            ->where('channel', $channel->sid)
            ->where('ts', '<', $timestamp)
            ->orderByDesc('ts')
            ->limit(self::UP_LIMIT)
            ->get()
            ->reverse()
            ->values()
            ->all();

        return [
            'firstLog' => $before === [] ? ($after[0] ?? null) : end($before),
            'logs' => array_merge($before, $after),
            'moreup' => count($before) === self::UP_LIMIT ? (string) reset($before)->getKey() : null,
            'moredown' => count($after) === self::DOWN_LIMIT ? (string) end($after)->getKey() : null,
        ];
    }

    /** @return array<int, array<int, array<int, CarbonImmutable>>> */
    public function timeline(Channel $channel): array
    {
        $first = Message::query()->where('channel', $channel->sid)->orderBy('ts')->first();
        $last = Message::query()->where('channel', $channel->sid)->orderByDesc('ts')->first();

        if ($first === null || $last === null) {
            return [];
        }

        $timeline = [];
        $date = $first->getCarbon()->startOfDay();
        $end = $last->getCarbon()->startOfDay();

        while ($date->lessThanOrEqualTo($end)) {
            $timeline[(int) $date->year][(int) $date->month][(int) $date->day] = $date;
            $date = $date->addDay();
        }

        return $timeline;
    }

    /** @return array<int, Message> */
    public function textSearch(Channel $channel, string $query): array
    {
        return Message::query()
            ->where('channel', $channel->sid)
            ->where('text', 'regex', new Regex(preg_quote($query, '/'), 'iu'))
            ->orderByDesc('ts')
            ->limit(self::UP_LIMIT + self::DOWN_LIMIT)
            ->get()
            ->all();
    }

    /** @return array{logs: array<int, Message>, more: ?string} */
    public function infinite(Channel $channel, Message $message, string $direction): array
    {
        abort_unless(in_array($direction, ['up', 'down'], true), 404);

        $query = Message::query()
            ->where('channel', $channel->sid)
            ->where('ts', $direction === 'up' ? '<' : '>', (string) $message->ts)
            ->orderBy('ts', $direction === 'up' ? 'desc' : 'asc')
            ->limit(self::DOWN_LIMIT);

        $logs = $query->get()->all();
        $more = count($logs) === self::DOWN_LIMIT ? (string) end($logs)->getKey() : null;

        if ($direction === 'up') {
            $logs = array_reverse($logs);
        }

        return compact('logs', 'more');
    }
}
