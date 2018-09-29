<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('callback', function (\Illuminate\Http\Request $request) {
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
        $events = $bot->parseEventRequest($request->getContent(), $signature[0]);
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
});
