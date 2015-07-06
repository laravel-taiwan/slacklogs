<?php

namespace App\Http\Controllers;


use App\Channel;
use App\Repository\MessageRepository;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * @var Request
     */
    private $request;
    /**
     * @var MessageRepository
     */
    private $messageRepo;

    /**
     * @param Request $request
     * @param MessageRepository $messageRepo
     */
    public function __construct(Request $request, MessageRepository $messageRepo)
    {
        $this->request = $request;
        $this->messageRepo = $messageRepo;
    }

    public function showChannel($channel, $date = null)
    {
        $channel = Channel::where('name', $channel)->firstOrFail();

        if (! $channel->is_member) {
            $error = "Sorry, @phptwbot is not in this channel, Plase /invite @phptwbot in #$channel->name First";

            return response($error, 403);
        }

        $datetime = null;

        if ($date) {
            $datetime = \DateTime::createFromFormat($this->datetimFormat, $date);
        }

        if ($datetime) {
            $result = $this->messageRepo->getAroundDate($channel, $datetime);
        } else {
            $result = $this->messageRepo->getLatest($channel);
        }

        //$timeline = MessageRepository::getTimeLine($channel);

        return response()->json($result);

    }

}