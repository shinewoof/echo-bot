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
        $signature = $this->service->resolveSignature($request);

        if (empty($signature)) {
            return response('Bad Request', 400);
        }

        // Check request with signature and parse request
        try {
            $events = $this->bot->parseEventRequest($request->getContent(), $signature);
        } catch (InvalidSignatureException $e) {
            return response('Invalid signature', 400);
        } catch (InvalidEventRequestException $e) {
            return response("Invalid event request", 400);
        }

        foreach ($events as $event) {
            if (!($event instanceof LINEBot\Event\MessageEvent)) {
                continue;
            }

            if (!($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage)) {
                continue;
            }

            if (!($event instanceof \LINE\LINEBot\Event\MessageEvent\LocationMessage)) {
                continue;
            }

            $replyText = $event->getText();
            $this->bot->replyText($event->getReplyToken(), $replyText);
        }

        return response('OK!', 200);
    }
}
