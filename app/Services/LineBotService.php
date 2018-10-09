<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 2018/9/30
 * Time: 上午 12:04
 */

namespace App\Services;

use App\Services\CallbackManager\Contracts\CallbackContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use LINE\LINEBot;
use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Response;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
use LINE\LINEBot\Exception\InvalidSignatureException;
use LINE\LINEBot\Exception\InvalidEventRequestException;

class LineBotService
{
    protected $lineUserId;

    public function __construct($lineUserId = null)
    {
        $this->lineUserId = $lineUserId ?? config('line.line.LINE_USER_ID');
    }

    /**
     * @param TemplateMessageBuilder|string $content
     * @return Response
     */
    public function pushMessage($content): Response
    {
        if (is_string($content)) {
            $content = new TextMessageBuilder($content);
        }
        return $this->lineBot->pushMessage($this->lineUserId, $content);
    }

    public function getSignature(Request $request)
    {
        return $request->header(HTTPHeader::LINE_SIGNATURE);
    }

    public function replyMessage(CallbackContract $callback, Request $request)
    {
        $signature = $this->getSignature($request);

        if (empty($signature)) {
            return response('Bad Request', 400);
        }
        try {
            /**
             * @var LINEBot $lineBot
             */
            $lineBot = app('line.bot');

            $events = $lineBot->parseEventRequest($request->getContent(), $signature);

            foreach ($events as $event) {
                $message = $callback->handler();
                $message($event, $lineBot);
            }
        } catch (InvalidSignatureException $e) {
            return response('Invalid signature', 400);
        } catch (InvalidEventRequestException $e) {
            return response("Invalid event request", 400);
        }

        return response('OK!', 200);

    }
}