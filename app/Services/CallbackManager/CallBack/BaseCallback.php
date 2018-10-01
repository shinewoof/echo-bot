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
        $this->lineBot = $this->app->make(LINEBot::class);
    }

    /**
     * @param TextMessage $event
     * @throws \ReflectionException
     */
    public function message(TextMessage $event)
    {
        $replyText = $event->getText();
        $this->lineBot->replyText($event->getReplyToken(), $replyText);
    }
}