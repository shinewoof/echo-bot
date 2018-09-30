<?php

namespace Tests\Feature;

use LINE\LINEBot\Constant\HTTPHeader;
use Tests\TestCase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LineMessageEventTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @group LineMessageTest
     * @test
     */
    public function testRequestTextMessage()
    {
        $input = '{"events":[{"type":"message","replyToken":"ba423901a3f349be801537fbedc78936","source":{"userId":"U167f2467e32c933ba5ba335f376ec1c5","type":"user"},"timestamp":1482219839401,"message":{"type":"text","id":"5376324301443","text":"Hello Word"}}]}';

        $channelSecret = config('line.line.LINEBOT_SECRET'); // Channel secret string
        $httpRequestBody = $input; // Request body string
        $hash = hash_hmac('sha256', $httpRequestBody, $channelSecret, true);
        $signature = base64_encode($hash);

        $response = $this->call(
            'post',
            route('line.bot.message'),
            [],
            [],
            [],
            $this->transformHeadersToServerVars([HTTPHeader::LINE_SIGNATURE => $signature]),
            $input
        );


        $this->assertTrue($response instanceof TestResponse);

        $this->assertEquals(200, $response->getStatusCode());

    }

    public function testRequestLocationMessage()
    {
        $input = '{"events": [{"replyToken": "nHuyWiB7yP5Zw52FIkcQobQuGDXCTA","type": "message","timestamp": 1462629479859,"source": {"type": "user","userId": "U4af4980629..."},"message": {"id": "325708","type": "location","title": "my location","address": "〒150-0002 東京都渋谷区渋谷２丁目２１−１","latitude": 35.65910807942215,"longitude": 139.70372892916203}}]}';

        $channelSecret = config('line.line.LINEBOT_SECRET'); // Channel secret string
        $httpRequestBody = $input; // Request body string
        $hash = hash_hmac('sha256', $httpRequestBody, $channelSecret, true);
        $signature = base64_encode($hash);

        $response = $this->call(
            'post',
            route('line.bot.message', ['userId' => 'weather']),
            [],
            [],
            [],
            $this->transformHeadersToServerVars([HTTPHeader::LINE_SIGNATURE => $signature]),
            $input
        );


        $this->assertTrue($response instanceof TestResponse);

        $this->assertEquals(200, $response->getStatusCode());

    }
}
