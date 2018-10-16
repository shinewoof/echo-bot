<?php

namespace App\Listeners;

use App\Services\CallbackManager\Contracts\CallbackContract;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use LINE\LINEBot\Event\MessageEvent;

class TextEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(MessageEvent $event)
    {
        try {
            /**
             * @var CallbackContract $callback
             */
            $callback = app('callback.manager')->getCallback($event->userId);
            $callback->message($event);
        } catch (\Exception $ex) {
            Log::debug($ex->getFile() . '@'. $ex->getLine() . ':' . $ex->getMessage());

            $this->app->make('line.bot')
                ->replyText($event->getReplyToken(), '抱歉出了點問題!');
        }

    }
}
