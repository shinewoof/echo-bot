<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/callback', function (Request $request) {

    logger("message request : ", $request->all());
});

Route::post('callback', function (\Illuminate\Http\Request $request) {
    $signature = $request->header(\LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE);

    if (empty($signature)) {
        return response('Bad Request', 400);
    }

    /**
     * @var \LINE\LINEBot $bot
     */

    $bot = App::make(\LINE\LINEBot::class);

    // Check request with signature and parse request
    try {
        $events = $bot->parseEventRequest($request->getContent(), $signature);
    } catch (\LINE\LINEBot\Exception\InvalidSignatureException $e) {
        return response('Invalid signature', 400);
    } catch (\LINE\LINEBot\Exception\InvalidEventRequestException $e) {
        return response("Invalid event request", 400);
    }

    foreach ($events as $event) {
        if (!($event instanceof \LINE\LINEBot\Event\MessageEvent)) {
            continue;
        }

        if (!($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage)) {
            continue;
        }

        $replyText = $event->getText();
        $bot->replyText($event->getReplyToken(), $replyText);
    }

    return response('OK!', 200);
})->name('line.bot.message');