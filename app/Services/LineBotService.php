<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 2018/9/30
 * Time: 上午 12:04
 */

namespace App\Services;

use Illuminate\Support\Facades\App;
use LINE\LINEBot;
use LINE\LINEBot\Response;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;

class LineBotService
{
    protected $lineUserId;
    /**
     * @var LINEBot
     */
    protected $lineBot;

    public function __construct($lineUserId = null)
    {
        $this->lineUserId = $lineUserId ?? config('line.line.LINE_USER_ID');
        $this->lineBot = App::make(LINEBot::class);
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

}