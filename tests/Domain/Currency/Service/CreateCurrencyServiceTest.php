<?php

namespace App\Tests\Domain\Currency\Service;

use App\Domain\Currency\Entity\Currency;
use App\Domain\Currency\Exception\CurrencyAlreadyCreatedException;
use App\Domain\Currency\Exception\CurrencyDeactivatedException;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Domain\Currency\Service\CreateCurrencyService;
use App\Tests\Fixture\Currency\CurrencyFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateCurrencyServiceTest extends KernelTestCase
{
    public function testCreateCurrencyService()
    {
        $repositoryMock = $this->createMock(CurrencyRepositoryInterface::class);

        $service = new CreateCurrencyService($repositoryMock);

        $service->create('RUB', 810, 'Russian ruble');

        $repositoryMock->method('ofCode')->willReturn(CurrencyFixture::getOne(
            'RUB',
            810,
            'Russian ruble'
        ));

        $currency = $repositoryMock->ofCode('RUB');

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals('RUB', $currency->getCode());
        $this->assertEquals(810, $currency->getNum());
        $this->assertEquals('Russian ruble', $currency->getName());
        $this->assertTrue($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNull($currency->getUpdatedAt());
    }

    public function testCreateCurrencyAlreadyCreatedService()
    {
        $repositoryMock = $this->createMock(CurrencyRepositoryInterface::class);
        $repositoryMock->method('ofCode')->willReturn(CurrencyFixture::getOne(
            'RUB',
            810,
            'Russian ruble'
        ));

        $service = new CreateCurrencyService($repositoryMock);

        $this->expectException(CurrencyAlreadyCreatedException::class);
        $service->create('RUB', 810, 'Russian ruble');
    }

    public function testCreateCurrencyDeactivatedService()
    {
        $repositoryMock = $this->createMock(CurrencyRepositoryInterface::class);
        $repositoryMock->method('ofCodeDeactivated')->willReturn(CurrencyFixture::getOneDeactivated(
            'RUB',
            810,
            'Russian ruble',
            isActive: false
        ));

        $service = new CreateCurrencyService($repositoryMock);

        $this->expectException(CurrencyDeactivatedException::class);
        $service->create('RUB', 810, 'Russian ruble');
    }
}
