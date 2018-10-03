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

            $callback = $this->app->make('callback.manager')->getCallback($userId);

            $response = $this->service->replyMessage($callback, $request);
        } catch (\Exception $e) {
            var_dump($e->getFile());
            var_dump($e->getLine());
            var_dump($e->getMessage());

            $response = response('there are something error!!', 400);
        }

        return $response;
    }
}
