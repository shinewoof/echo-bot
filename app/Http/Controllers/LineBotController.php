<?php

namespace App\Http\Controllers;

use App\Services\LineBotService;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use LINE\LINEBot;
use LINE\LINEBot\Exception\InvalidSignatureException;
use LINE\LINEBot\Exception\InvalidEventRequestException;

class LineBotController extends Controller
{
    /**
     * @var Application
     */
    protected $app;
    /**
     * @var LineBotService
     */
    protected $service;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->service = $this->app->make(LineBotService::class);
    }

    public function receive(Request $request, $userId)
    {
        try {
            $signature = $this->service->getSignature($request);

            /**
             * @var LINEBot $lineBot
             */
            $lineBot = app('line.bot');

            $events = $lineBot->parseEventRequest($request->getContent(), $signature);

            foreach ($events as $event) {
                $event->userId = $userId;
                event($event);
            }

            $response = response('OK', 200);
        } catch (\Exception $ex) {
            logger($ex->getFile() . '@'. $ex->getLine() . ':' . $ex->getMessage());

            $response = response('there are something error!!', 400);
        }

        return $response;
    }
}
