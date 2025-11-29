<?php

namespace App\Tests\Application\Currency\Query;

use App\Application\Currency\Query\FindCurrencyByCodeQuery;
use App\Application\Currency\Query\FindCurrencyByCodeQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Domain\Currency\Entity\Currency;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\MessageBusTestCase;

class FindCurrencyByCodeQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindCurrencyByCodeQuery('RUB')
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindCurrencyByCodeQueryHandler::class)
        );
    }

    public function testFindCurrencyByCodeQueryHandler()
    {
        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $container = $this->getContainer();

        $repository = $container->get(CurrencyRepositoryInterface::class);
        $repository->create($newCurrency);

        $currency = $container->get(FindCurrencyByCodeQueryHandler::class)(
            new FindCurrencyByCodeQuery($newCurrency->getCode())
        );

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
