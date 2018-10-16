<?php
namespace App\Services\CallbackManager\Callback;

use App\Services\CallbackManager\Contracts\CallbackContract;
use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use LINE\LINEBot\Event\MessageEvent;

abstract class BaseCallback implements CallbackContract
{
    protected $app;

    protected $lineBot;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function getHttpClient()
    {
        return new Client();
    }

    abstract public function message(MessageEvent $event);
    abstract public function location(MessageEvent $event);
}