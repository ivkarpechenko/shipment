<?php

namespace App\Tests\Infrastructure\Http\Currency\v1;

use App\Domain\Currency\Entity\Currency;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\HttpTestCase;

class UpdateCurrencyControllerTest extends HttpTestCase
{
    public function testUpdateCurrencyName()
    {
        $repository = $this->getContainer()->get(CurrencyRepositoryInterface::class);
        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');

        $repository->create($newCurrency);

        $this->client->request(
            'PUT',
            "/api/v1/currency/{$newCurrency->getCode()}",
            [
                'name' => 'Updated russian ruble',
            ]
        );

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(200);

        $currency = $repository->ofCode($newCurrency->getCode());

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals('RUB', $currency->getCode());
        $this->assertEquals(810, $currency->getNum());
        $this->assertEquals('Updated russian ruble', $currency->getName());
        $this->assertTrue($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNotNull($currency->getUpdatedAt());
    }

    public function testUpdateCurrencyIsActive()
    {
        $repository = $this->getContainer()->get(CurrencyRepositoryInterface::class);
        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');

        $repository->create($newCurrency);

        $this->client->request(
            'PUT',
            "/api/v1/currency/{$newCurrency->getCode()}",
            [
                'isActive' => false,
            ]
        );

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(200);

        $currency = $repository->ofCodeDeactivated($newCurrency->getCode());

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals('RUB', $currency->getCode());
        $this->assertEquals(810, $currency->getNum());
        $this->assertEquals('Russian ruble', $currency->getName());
        $this->assertFalse($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNotNull($currency->getUpdatedAt());
    }
}
