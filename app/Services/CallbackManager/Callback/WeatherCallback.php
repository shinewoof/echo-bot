<?php

namespace App\Services\CallbackManager\Callback;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use LINE\LINEBot\Constant\Flex\ComponentAlign;
use LINE\LINEBot\Constant\Flex\ComponentFontSize;
use LINE\LINEBot\Constant\Flex\ComponentFontWeight;
use LINE\LINEBot\Constant\Flex\ComponentGravity;
use LINE\LINEBot\Constant\Flex\ComponentImageAspectMode;
use LINE\LINEBot\Constant\Flex\ComponentImageAspectRatio;
use LINE\LINEBot\Constant\Flex\ComponentLayout;
use LINE\LINEBot\Constant\Flex\ComponentSpacing;
use LINE\LINEBot\Event\MessageEvent;
use Exception;
use LINE\LINEBot\Exception\CurlExecutionException;
use LINE\LINEBot\MessageBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ImageComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\SeparatorComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;
use LINE\LINEBot\MessageBuilder\FlexMessageBuilder;
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
     * @throws Exception
     * @throws GuzzleException
     * @throws ReflectionException
     */
    public function location(MessageEvent $event)
    {
        try {
            $response = $this->requestWeather($event);
            $messageBuilder = $this->parseForecast5Day($response->getBody());

            $response = $this->app->make('line.bot')
                ->replyMessage($event->getReplyToken(), $messageBuilder);
        } catch (GuzzleException $ex) {
            throw new Exception('request weather error');
        } catch (CurlExecutionException $ex) {
            throw new Exception($ex->getMessage());
        }

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
        $lat = intval($event->getLatitude());
        $lon = intval($event->getLongitude());

        $url = "{$domain}?lat={$lat}&lon={$lon}&APPID={$apiKey}&units=metric&cnt=5";

        $client = $this->getHttpClient();
        $request = new Request('GET', $url);
        return $client->send($request);
    }

    /**
     * @param string $content
     * @return MessageBuilder
     */
    protected function parseForecast5Day(string $content): MessageBuilder
    {
        $images = [];
        $list = [];
        $content = json_decode($content, true);

        foreach ($content['list'] as $data) {
            array_push($images, "https://openweathermap.org/img/w/{$data['weather'][0]['icon']}.png");
            $date = $data['dt_txt'];
            $temperature = $data['main']['temp'] . "°C";
            array_push($list, $date, 'separator', $temperature, 'separator');
        }
        array_pop($list);

        $container = BubbleContainerBuilder::builder()
            ->setHeader(
                BoxComponentBuilder::builder()
                    ->setLayout(ComponentLayout::HORIZONTAL)
                    ->setContents([
                        TextComponentBuilder::builder()
                            ->setText("最近天氣預報")
                            ->setSize(ComponentFontSize::SM)
                            ->setWeight(ComponentFontWeight::BOLD)
                    ])

            )
            ->setBody(
                BoxComponentBuilder::builder()
                    ->setLayout(ComponentLayout::HORIZONTAL)
                    ->setSpacing(ComponentSpacing::MD)
                    ->setContents([
                        BoxComponentBuilder::builder()
                            ->setLayout(ComponentLayout::VERTICAL)
                            ->setFlex(1)
                            ->setContents(array_map(function ($imageUrl) {
                                return ImageComponentBuilder::builder()
                                    ->setUrl($imageUrl)
                                    ->setAlign(ComponentAlign::START)
                                    ->setSize(ComponentFontSize::XS)
                                    ->setAspectRatio(ComponentImageAspectRatio::R4TO3)
                                    ->setAspectMode(ComponentImageAspectMode::COVER);
                            }, $images)),
                        BoxComponentBuilder::builder()
                            ->setLayout(ComponentLayout::VERTICAL)
                            ->setFlex(1)
                            ->setContents(array_map(function ($text) {
                                if ($text == 'separator') {
                                    return SeparatorComponentBuilder::builder();
                                }

                                return TextComponentBuilder::builder()
                                    ->setText((string) $text)
                                    ->setFlex(2)
                                    ->setSize(ComponentFontSize::XS)
                                    ->setGravity(ComponentGravity::TOP);
                            }, $list)),
                    ])
            );

        return FlexMessageBuilder::builder()
            ->setAltText("")
            ->setContents($container);
    }
}