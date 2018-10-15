<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'LINE\LINEBot\Event\MessageEvent\AudioMessage' => [
            'App\Listeners\AudioEventListener',
        ],
        'LINE\LINEBot\Event\MessageEvent\FileMessage' => [
            'App\Listeners\FileEventListener',
        ],
        'LINE\LINEBot\Event\MessageEvent\ImageMessage' => [
            'App\Listeners\ImageEventListener',
        ],
        'LINE\LINEBot\Event\MessageEvent\LocationMessage' => [
            'App\Listeners\LocationEventListener',
        ],
        'LINE\LINEBot\Event\MessageEvent\StickerMessage' => [
            'App\Listeners\StickerEventListener',
        ],
        'LINE\LINEBot\Event\MessageEvent\TextMessage' => [
            'App\Listeners\TextEventListener',
        ],
        'LINE\LINEBot\Event\MessageEvent\UnknownMessage' => [
            'App\Listeners\UnknownEventListener',
        ],
        'LINE\LINEBot\Event\MessageEvent\VideoMessage' => [
            'App\Listeners\VideoEventListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
