<?php

namespace Tests\Unit\Services;

use App\Services\LineBotService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LineBotServiceTest extends TestCase
{
    /** @var  LineBotService */
    private $lineBotService;

    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->lineBotService = app(LineBotService::class);
    }

    public function tearDown()
    {
        parent::tearDown(); // TODO: Change the autogenerated stub
    }

    public function testPushMessage()
    {
        $this->markTestSkipped('OK!');
        // $response = $this->lineBotService->pushMessage('Test from laravel.');
        // $this->assertEquals(200, $response->getHTTPStatus());
    }
}
