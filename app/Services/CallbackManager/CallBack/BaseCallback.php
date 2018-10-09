<?php

namespace App\Services\CallbackManager\CallBack;


use App\Services\CallbackManager\Contracts\CallbackContract;
use Illuminate\Foundation\Application;
use LINE\LINEBot;
use LINE\LINEBot\Event\MessageEvent\TextMessage;

class BaseCallback implements CallbackContract
{
    protected $app;

    protected $lineBot;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @return \Closure
     */
    public function handler()
    {
        return function (TextMessage $event, LINEBot $bot) {
            $replyText = $event->getText();
            $bot->replyText($event->getReplyToken(), $replyText);
        };
    }
}