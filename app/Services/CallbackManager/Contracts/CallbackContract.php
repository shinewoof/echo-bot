<?php
/**
 * Created by PhpStorm.
 * User: USER
 * Date: 2018/10/1
 * Time: 下午 10:49
 */

namespace App\Services\CallbackManager\Contracts;

use LINE\LINEBot\Event\MessageEvent\TextMessage;

interface CallbackContract
{
    public function handler();
}