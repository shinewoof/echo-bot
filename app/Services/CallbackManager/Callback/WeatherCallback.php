<?php

namespace App\Services\CallbackManager\Callback;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use LINE\LINEBot\Event\MessageEvent;
use Exception;
use ReflectionException;
use GuzzleHttp\Exception\GuzzleException;

class WeatherCallback extends BaseCallback
{
    /**
     * @param MessageEvent $event
     * @return \LINE\LINEBot\Response
     * @throws ReflectionException
     */
    public function message(MessageEvent $event)
    {
        return $this->app->make('line.bot')
            ->replyText($event->getReplyToken(), $event->getText());
    }

    /**
     * @param MessageEvent $event
     * @return \LINE\LINEBot\Response
     * @throws GuzzleException
     * @throws ReflectionException
     */
    public function location(MessageEvent $event)
    {
        $response = $this->requestWeather($event);
        $replyText = $this->parseForecast5Day($response->getBody());

        return $this->app->make('line.bot')
            ->replyText($event->getReplyToken(), $replyText);
    }

    /**
     * @param $event
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function requestWeather($event): Response
    {
        $apiKey = config('weather.api_key');
        $domain = config('weather.url.forecast');
        $lat = urlencode($event->getLatitude());
        $lon = urlencode($event->getLongitude());

        $url = "{$domain}?lat={$lat}&lon={$lon}&APPID={$apiKey}&units=metric";

        $client = $this->getHttpClient();
        $request = new Request('GET', $url);
        return $client->send($request);
    }

    /**
     * @param string $content
     * @return string
     */
    protected function parseForecast5Day(string $content): string
    {
        $list = [];
        $content = json_decode($content, true);

        foreach ($content['list'] as $data) {
            $date = $data['dt_txt'];
            $temperature = $data['main']['temp'];
            $weather = $data['weather'][0]['description'];
            array_push($list, implode(', ', [$date, $temperature, $weather]));
        }
        return implode('\n', $list);
    }
}