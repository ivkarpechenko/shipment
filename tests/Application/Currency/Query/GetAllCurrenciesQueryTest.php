<?php

namespace App\Tests\Application\Currency\Query;

use App\Application\Currency\Query\GetAllCurrenciesQuery;
use App\Application\Currency\Query\GetAllCurrenciesQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Domain\Currency\Entity\Currency;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\MessageBusTestCase;

class GetAllCurrenciesQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new GetAllCurrenciesQuery()
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(GetAllCurrenciesQueryHandler::class)
        );
    }

    public function testGetAllCurrenciesQueryHandler()
    {
        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $container = $this->getContainer();

        $repository = $container->get(CurrencyRepositoryInterface::class);
        $repository->create($newCurrency);

        $currencies = $container->get(GetAllCurrenciesQueryHandler::class)(
            new GetAllCurrenciesQuery()
        );

        $this->assertNotEmpty($currencies);
        $this->assertIsArray($currencies);

        $currency = reset($currencies);

        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals('RUB', $currency->getCode());
        $this->assertEquals(810, $currency->getNum());
        $this->assertEquals('Russian ruble', $currency->getName());
        $this->assertTrue($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNull($currency->getUpdatedAt());
    }
}
