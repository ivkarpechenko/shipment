<?php

namespace App\Tests\Domain\Currency\Service;

use App\Domain\Currency\Entity\Currency;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Domain\Currency\Service\UpdateCurrencyService;
use App\Tests\Fixture\Currency\CurrencyFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UpdateCurrencyServiceTest extends KernelTestCase
{
    public function testUpdateCurrencyName()
    {
        $repositoryMock = $this->createMock(CurrencyRepositoryInterface::class);
        $repositoryMock->method('ofCode')->willReturn(CurrencyFixture::getOne(
            'RUB',
            810,
            'Russian ruble'
        ));

        $service = new UpdateCurrencyService($repositoryMock);

        $service->update('RUB', 'Updated russian ruble', null);

        $repositoryMock->method('ofCode')->willReturn(CurrencyFixture::getOne(
            'RUB',
            810,
            'Updated russian ruble'
        ));

        $currency = $repositoryMock->ofCode('RUB');

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
        $repositoryMock = $this->createMock(CurrencyRepositoryInterface::class);
        $repositoryMock->method('ofCode')->willReturn(CurrencyFixture::getOne(
            'RUB',
            810,
            'Russian ruble'
        ));

        $service = new UpdateCurrencyService($repositoryMock);

        $service->update('RUB', null, false);

        $repositoryMock->method('ofCode')->willReturn(CurrencyFixture::getOneDeactivated(
            'RUB',
            810,
            'Russian ruble',
            isActive: false
        ));

        $currency = $repositoryMock->ofCode('RUB');

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
