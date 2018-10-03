<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 2018/10/1
 * Time: 下午 11:06
 */

namespace App\Services\CallbackManager\Providers;


use App\Services\CallbackManager\Callback\WeatherCallback;
use App\Services\CallbackManager\CallbackManager;
use Carbon\Laravel\ServiceProvider;

class CallbackProvider extends ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->registerAlias();
        $this->registerCallBackManager();
    }

    protected function registerCallBackManager()
    {
        $this->app->singleton('callback.manager', function ($app) {
            $manager = new CallbackManager($app);
            $this->registerCallBack($manager);
            return $manager;
        });
    }

    protected function registerCallback(CallbackManager $manager)
    {
        $manager->addCallBack('weather', WeatherCallback::class);
    }

    protected function registerAlias()
    {
        $this->app->alias('callback.manager', CallbackManager::class);
    }


    /**
     * @return array
     */
    public function provides()
    {
        return [
            'callback.manager', CallbackManager::class,
        ];
    }
}