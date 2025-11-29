<?php

namespace App\Tests\Infrastructure\Http\Currency\v1;

use App\Domain\Currency\Entity\Currency;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Tests\HttpTestCase;

class CreateCurrencyControllerTest extends HttpTestCase
{
    public function testCreateCurrencyRoute()
    {
        $this->client->request(
            'POST',
            '/api/v1/currency',
            [
                'code' => 'RUB',
                'name' => 'Russian ruble',
                'num' => 810,
            ]
        );

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(201);

        $repository = $this->getContainer()->get(CurrencyRepositoryInterface::class);

        $currency = $repository->ofCode('RUB');

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals('RUB', $currency->getCode());
        $this->assertEquals(810, $currency->getNum());
        $this->assertEquals('Russian ruble', $currency->getName());
        $this->assertTrue($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNull($currency->getUpdatedAt());
    }
}
