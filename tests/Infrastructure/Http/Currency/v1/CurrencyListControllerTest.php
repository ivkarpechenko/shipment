<?php

namespace App\Tests\Infrastructure\Http\Currency\v1;

use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\HttpTestCase;

class CurrencyListControllerTest extends HttpTestCase
{
    public function testGetAllRoute()
    {
        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $repository = $this->getContainer()->get(CurrencyRepositoryInterface::class);
        $repository->create($newCurrency);

        $this->client->request('GET', '/api/v1/currency');

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

    public function testAllByPaginateRoute()
    {
        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $repository = $this->getContainer()->get(CurrencyRepositoryInterface::class);
        $repository->create($newCurrency);

        $this->client->request('GET', '/api/v1/currency/paginate?page=0&offset=1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('data', $response);
        $this->assertStringContainsString('total', $response);
        $this->assertStringContainsString('pages', $response);

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
