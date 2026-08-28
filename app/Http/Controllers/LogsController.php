<?php

namespace App\Http\Controllers;

use App\Domain\Channel;
use App\Domain\Message;
use App\Repositories\MessageRepository;
use DateTimeImmutable;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class LogsController extends Controller
{
    public function __construct(private readonly MessageRepository $messages)
    {
    }

    public function channel(Request $request, string $chan, ?string $date = null): View|Response
    {
        $channel = Channel::query()->where('name', $chan)->firstOrFail();

        if (! $channel->is_member) {
            return response("Sorry, the archive bot is not in #{$channel->name}. Invite it before importing messages.", 404);
        }

        $datetime = $this->parseDate($date);
        $result = $datetime
            ? $this->messages->aroundDate($channel, $datetime)
            : $this->messages->latest($channel);

        return $this->renderLogs($request, $channel, $chan, $result);
    }

    public function datetime(Request $request, string $chan, string $date, string $time): View|Response
    {
        return $this->channel($request, $chan, "$date/$time");
    }

    public function search(Request $request, string $chan, ?string $query = null): View|Response
    {
        $query ??= $request->string('q')->toString();

        if ($query === '') {
            return $this->channel($request, $chan);
        }

        $channel = Channel::query()->where('name', $chan)->firstOrFail();
        $logs = $this->messages->textSearch($channel, $query);

        if ($request->ajax()) {
            if ($logs === []) {
                return response('<p>No results were found.</p>');
            }

            return response()->view('partials.logs', compact('logs', 'chan') + ['search' => $query]);
        }

        $channels = Channel::query()->where('is_member', true)->orderBy('name')->get();
        $timeline = $this->messages->timeline($channel);

        return view('logs', compact('channels', 'timeline', 'logs', 'chan') + ['search' => $query]);
    }

    public function infinite(string $chan, string $direction, string $id): View
    {
        $channel = Channel::query()->where('name', $chan)->firstOrFail();
        $result = $this->messages->infinite($channel, Message::query()->findOrFail($id), $direction);

        return view('partials.logs', ['logs' => $result['logs'], 'chan' => $chan])
            ->with('more'.$direction, $result['more']);
    }

    private function parseDate(?string $date): ?DateTimeImmutable
    {
        if ($date === null) {
            return null;
        }

        foreach (['!Y-m-d/H:i:s', '!Y-m-d/H:i', '!Y-m-d'] as $format) {
            $datetime = DateTimeImmutable::createFromFormat($format, $date);
            if ($datetime !== false && DateTimeImmutable::getLastErrors() === false) {
                return $datetime;
            }
        }

        abort(404, 'Invalid archive date.');
    }

    /** @param array{firstLog: ?Message, logs: array<int, Message>, moreup: ?string, moredown: ?string} $result */
    private function renderLogs(Request $request, Channel $channel, string $chan, array $result): View
    {
        $data = $result + [
            'chan' => $chan,
            'channels' => Channel::query()->where('is_member', true)->orderBy('name')->get(),
            'timeline' => $this->messages->timeline($channel),
        ];

        return view($request->ajax() ? 'partials.logs' : 'logs', $data);
    }
}
