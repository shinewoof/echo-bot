<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

class LineBotProvider extends ServiceProvider
{
    protected $defer = true;
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('line.bot', function () {
            $httpClient = new CurlHTTPClient(config('line.line.LINEBOT_TOKEN'));
            return new LINEBot($httpClient, ['channelSecret' => config('line.line.LINEBOT_SECRET')]);
        });

        $this->app->alias('line.bot',LINEBot::class);
    }

    public function provides()
    {
        return [
            'line.bot', LINEBot::class,
        ];
    }

}
