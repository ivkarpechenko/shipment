<?php

namespace App\Tests\Infrastructure\DBAL\Repository\Doctrine\Currency;

use App\Domain\Currency\Entity\Currency;
use App\Infrastructure\DBAL\Repository\Doctrine\Currency\DoctrineCurrencyRepository;
use App\Tests\DoctrineTestCase;
use App\Tests\Fixture\Currency\CurrencyFixture;

class DoctrineCurrencyRepositoryTest extends DoctrineTestCase
{
    private DoctrineCurrencyRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->getContainer()->get(DoctrineCurrencyRepository::class);
    }

    public function testCreateCurrency()
    {
        $this->assertEmpty($this->repository->all());

        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');

        $this->repository->create($newCurrency);

        $currency = $this->repository->ofId($newCurrency->getId());

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals($newCurrency->getId(), $currency->getId());
        $this->assertEquals($newCurrency->getCode(), $currency->getCode());
        $this->assertEquals($newCurrency->getNum(), $currency->getNum());
        $this->assertEquals($newCurrency->getName(), $currency->getName());
    }

    public function testUpdateCurrency()
    {
        $this->assertEmpty($this->repository->all());

        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $this->repository->create($newCurrency);

        $this->assertEquals('RUB', $newCurrency->getCode());

        $currency = $this->repository->ofId($newCurrency->getId());

        $currency->changeName('Updated russian ruble');
        $this->repository->update($currency);

        $updatedCurrency = $this->repository->ofId($currency->getId());

        $this->assertNotNull($updatedCurrency->getUpdatedAt());
        $this->assertEquals('Updated russian ruble', $updatedCurrency->getName());

        $this->assertTrue($updatedCurrency->isActive());

        $updatedCurrency->changeIsActive(false);
        $this->repository->update($updatedCurrency);

        $deactivatedCurrency = $this->repository->ofIdDeactivated($updatedCurrency->getId());

        $this->assertNotNull($deactivatedCurrency);
        $this->assertFalse($deactivatedCurrency->isActive());
    }

    public function testOfId()
    {
        $this->assertEmpty($this->repository->all());

        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $this->repository->create($newCurrency);

        $currency = $this->repository->ofId($newCurrency->getId());

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals($newCurrency->getId(), $currency->getId());
        $this->assertEquals($newCurrency->getCode(), $currency->getCode());
        $this->assertEquals($newCurrency->getNum(), $currency->getNum());
        $this->assertEquals($newCurrency->getName(), $currency->getName());
        $this->assertTrue($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNull($currency->getUpdatedAt());
    }

    public function testOfCode()
    {
        $this->assertEmpty($this->repository->all());

        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $this->repository->create($newCurrency);

        $currency = $this->repository->ofCode($newCurrency->getCode());

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals($newCurrency->getId(), $currency->getId());
        $this->assertEquals($newCurrency->getCode(), $currency->getCode());
        $this->assertEquals($newCurrency->getNum(), $currency->getNum());
        $this->assertEquals($newCurrency->getName(), $currency->getName());
        $this->assertTrue($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNull($currency->getUpdatedAt());
    }

    public function testOfIdDeactivated()
    {
        $this->assertEmpty($this->repository->all());

        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $newCurrency->changeIsActive(false);
        $this->repository->create($newCurrency);

        $currency = $this->repository->ofIdDeactivated($newCurrency->getId());

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals($newCurrency->getId(), $currency->getId());
        $this->assertEquals($newCurrency->getCode(), $currency->getCode());
        $this->assertEquals($newCurrency->getNum(), $currency->getNum());
        $this->assertEquals($newCurrency->getName(), $currency->getName());
        $this->assertFalse($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNotNull($currency->getUpdatedAt());
    }

    public function testOfCodeDeactivated()
    {
        $this->assertEmpty($this->repository->all());

        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $newCurrency->changeIsActive(false);
        $this->repository->create($newCurrency);

        $currency = $this->repository->ofCodeDeactivated($newCurrency->getCode());

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals($newCurrency->getId(), $currency->getId());
        $this->assertEquals($newCurrency->getCode(), $currency->getCode());
        $this->assertEquals($newCurrency->getNum(), $currency->getNum());
        $this->assertEquals($newCurrency->getName(), $currency->getName());
        $this->assertFalse($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNotNull($currency->getUpdatedAt());
    }

    public function testOfNumDeactivated()
    {
        $this->assertEmpty($this->repository->all());

        $newCurrency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');
        $newCurrency->changeIsActive(false);
        $this->repository->create($newCurrency);

        $currency = $this->repository->ofNumDeactivated($newCurrency->getNum());

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals($newCurrency->getId(), $currency->getId());
        $this->assertEquals($newCurrency->getCode(), $currency->getCode());
        $this->assertEquals($newCurrency->getNum(), $currency->getNum());
        $this->assertEquals($newCurrency->getName(), $currency->getName());
        $this->assertFalse($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNotNull($currency->getUpdatedAt());
    }
}
