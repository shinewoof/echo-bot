<?php

namespace App\Services\CallbackManager\CallBack;


use App\Services\CallbackManager\Contracts\CallbackContract;
use Illuminate\Foundation\Application;
use LINE\LINEBot\Event\MessageEvent;

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
    public function message(MessageEvent $event)
    {
        return $this->app->make('line.bot')
            ->replyText($event->getReplyToken(), $event->getText());
    }
}