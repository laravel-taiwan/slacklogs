<?php

class LogsController extends BaseController {

    /**
     * The format of dates
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d';

    /**
     * The format of datetime
     *
     * @var string
     */
    protected $datetimeFormat = 'Y-m-d/H:i';

    /**
     * The number of messages to load via AJAX
     *
     * @var int
     */
    protected $ajaxLoad = 200;

    public function index()
    {
        return View::make('index');
    }

    public function showChannel($chan, $date = null)
    {
        $channel = Channel::where('name', $chan)->firstOrFail();

        $datetime = null;

        if ($date) {
            $datetime = DateTime::createFromFormat($this->datetimeFormat, $date);
        }

        if ($date && !$datetime) {
            $datetime = DateTime::createFromFormat($this->dateFormat, $date);
        }

        if ($datetime) {
            list($firstLog, $logs, $moreup, $moredown) = Message\Repository::getAroundDate($channel, $datetime);
        } else {
            list($firstLog, $logs, $moreup) = Message\Repository::getLatest($channel);
        }


        $timeline = Message\Repository::getTimeLine($channel);

        return View::make(Request::ajax() ? 'partials.logs' : 'logs', compact('chan', 'logs', 'firstLog', 'moreup', 'moredown', 'timeline'));
    }

    public function search($chan, $q = null)
    {
        if (! $q) return $this->showChannel($chan);

        $channel = Channel::where('name', $chan)->firstOrFail();

        $logs = Message\Repository::textSearch($channel, $q);

        $view = Request::ajax() ? 'partials.logs' : 'logs';
        $search = $q;

        if (Request::ajax()) {
            if (! count($logs)) {
                return '<p>No results were found.</p>';
            }
        }

        return View::make($view, compact('logs', 'search', 'chan'));
    }

    public function infinite($chan, $direction, $id)
    {
        $channel = Channel::where('name', $chan)->firstOrFail();

        $log = Message::findOrFail($id);

        // Build the query to fetch logs with
        if ($direction == 'up') {
            $logs = Message::where('channel', $channel->sid)
                ->where('ts', '<', "$log->ts")
                ->orderBy('ts', 'desc')
                ->take($this->ajaxLoad)
                ->get();
        } else {
            $logs = Message::where('channel', $channel->sid)
                ->where('ts', '>', "$log->ts")
                ->orderBy('ts', 'asc')
                ->take($this->ajaxLoad)
                ->get();
        }

        $logs = Message\Repository::convertCollection($logs);

        $loadMore = null;

        if (count($logs) == $this->ajaxLoad) {
            $loadMore = end($logs)->_id;
        }

        if ($direction == 'up')
            $logs = array_reverse($logs);

        return View::make('partials.logs', compact('chan', 'logs'))
            ->with('more' . $direction, $loadMore);
    }

}
