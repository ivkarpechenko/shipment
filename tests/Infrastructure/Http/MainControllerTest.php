<?php

namespace App\Tests\Infrastructure\Http;

use App\Tests\HttpTestCase;

class MainControllerTest extends HttpTestCase
{
    public function testMainPage()
    {
        $this->client->request('GET', '/');

        $this->assertResponseStatusCodeSame(404);
    }
}
