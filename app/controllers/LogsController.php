<?php

use CL\Slack\Transport\ApiClient;

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function showWelcome()
	{
        $this->apiToken = 'xoxp-3246222755-3247683616-3287919231-12c7ab';
        $this->apiClient = new ApiClient($this->apiToken);
        $payload = new \CL\Slack\Payload\ChannelsInfoPayload();
        $payload->setChannelId('C041JNF2L');
        $response = $this->apiClient->send($payload);

		return View::make('hello');
	}

}
