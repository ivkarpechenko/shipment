<?php

namespace App\Tests\Infrastructure\Http\Currency\v1;

use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\HttpTestCase;

class FindCurrencyControllerTest extends HttpTestCase
{
    public function testFindById()
    {
        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $repository = $this->getContainer()->get(CurrencyRepositoryInterface::class);
        $repository->create($newCurrency);

        $this->client->request(
            'GET',
            "/api/v1/currency/find-by-id/{$newCurrency->getId()->toRfc4122()}"
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('code', $response);
        $this->assertStringContainsString('num', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);

        $this->assertStringContainsString('RUB', $response);
        $this->assertStringContainsString(810, $response);
        $this->assertStringContainsString('Russian ruble', $response);
    }

    public function testFindByCode()
    {
        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $repository = $this->getContainer()->get(CurrencyRepositoryInterface::class);
        $repository->create($newCurrency);

        $this->client->request(
            'GET',
            "/api/v1/currency/find-by-code/{$newCurrency->getCode()}"
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('code', $response);
        $this->assertStringContainsString('num', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);

        $this->assertStringContainsString('RUB', $response);
        $this->assertStringContainsString(810, $response);
        $this->assertStringContainsString('Russian ruble', $response);
    }

    public function testFindByNum()
    {
        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $repository = $this->getContainer()->get(CurrencyRepositoryInterface::class);
        $repository->create($newCurrency);

        $this->client->request(
            'GET',
            "/api/v1/currency/find-by-num/{$newCurrency->getNum()}"
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('code', $response);
        $this->assertStringContainsString('num', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);

        $this->assertStringContainsString('RUB', $response);
        $this->assertStringContainsString(810, $response);
        $this->assertStringContainsString('Russian ruble', $response);
    }
}
