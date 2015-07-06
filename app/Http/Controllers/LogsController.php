<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Repository\MessageRepository;
use Illuminate\Http\Request;

class LogsController extends Controller
{

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
    
    /**
     * @var
     */
    private $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function index()
    {

        return view('index');
        $general = Channel::getGeneralChannel();

        return redirect('/' . $general->name);
    }

    public function showChannel($channel, $date = null)
    {
        $channel = Channel::where('name', $channel)->firstOrFail();

        if (! $channel->is_member) {
            return "Sorry, @phptwbot is not in this channel, Plase /invite @phptwbot in #$channel->name First";
        }

        $datetime = null;

        if ($date) {
            $datetime = \DateTime::createFromFormat($this->datetimFormat, $date);
        }

        if ($date and !$datetime) {
            $datetime = \DateTime::createFromFormat($this->dateFormat, $date);
        }

        if ($datetime) {
            list($firstLog, $logs, $moreup, $moredown) = MessageRepository::getAroundDate($channel, $datetime);
        } else {
            list($firstLog, $logs, $moreup) = MessageRepository::getLatest($channel);
        }

        $timeline = MessageRepository::getTimeLine($channel);
        $view = $this->request->ajax() ? 'partials.logs' : 'logs';
        $channels = Channel::getIsMemberChannels();


        return view($view, compact('channels', 'channel', 'logs', 'firstLog', 'moreup', 'moredown', 'timeline'));
    }

}
