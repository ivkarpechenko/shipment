<?php

namespace App\Tests\Application\Currency\Query;

use App\Application\Currency\Query\GetCurrenciesByPaginateQuery;
use App\Application\Currency\Query\GetCurrenciesByPaginateQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Domain\Currency\Entity\Currency;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\MessageBusTestCase;

class GetCurrenciesByPaginateQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new GetCurrenciesByPaginateQuery(0, 1)
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(GetCurrenciesByPaginateQueryHandler::class)
        );
    }

    public function testGetCurrenciesByPaginateQueryHandler()
    {
        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $newCurrency2 = CurrencyFixture::getOne('KZ', 398, 'Kazakhstani tenge');
        $container = $this->getContainer();

        $repository = $container->get(CurrencyRepositoryInterface::class);
        $repository->create($newCurrency);
        $repository->create($newCurrency2);

        $currencies = $container->get(GetCurrenciesByPaginateQueryHandler::class)(
            new GetCurrenciesByPaginateQuery(0, 2)
        );

        $this->assertNotEmpty($currencies);
        $this->assertIsArray($currencies);
        $this->assertArrayHasKey('data', $currencies);
        $this->assertArrayHasKey('total', $currencies);
        $this->assertArrayHasKey('pages', $currencies);
        $this->assertCount(2, $currencies['data']);
        $this->assertEquals(2, $currencies['total']);
        $this->assertEquals(1, $currencies['pages']);

        $currency = reset($currencies['data']);

        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals('RUB', $currency->getCode());
        $this->assertEquals(810, $currency->getNum());
        $this->assertEquals('Russian ruble', $currency->getName());
        $this->assertTrue($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNull($currency->getUpdatedAt());
    }
}
