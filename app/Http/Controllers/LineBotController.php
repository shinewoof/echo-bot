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
        return $this->service->replyMessage($request->getContent(), $signature);
    }
}
