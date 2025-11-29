<?php

namespace App\Tests\Domain\Tax\Service;

use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Tax\Exception\TaxAlreadyCreatedException;
use App\Domain\Tax\Exception\TaxDeletedException;
use App\Domain\Tax\Repository\TaxRepositoryInterface;
use App\Domain\Tax\Service\CreateTaxService;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Tax\TaxFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateTaxServiceTest extends KernelTestCase
{
    public function testCreateTax()
    {
        $taxRepositoryMock = $this->createMock(TaxRepositoryInterface::class);
        $countryRepositoryMock = $this->createMock(CountryRepositoryInterface::class);
        $service = new CreateTaxService($taxRepositoryMock, $countryRepositoryMock);

        $countryRepositoryMock->method('ofCode')
            ->willReturn(CountryFixture::getOne('Russia', 'RU'));
        $country = $countryRepositoryMock->ofCode('RU');

        $service->create($country->getCode(), 'НДС', 0.2, 'price/(1+value)*value');

        $taxRepositoryMock->method('ofCountryAndName')
            ->willReturn(TaxFixture::getOne($country, 'НДС', 0.2, 'price/(1+value)*value'));

        $tax = $taxRepositoryMock->ofCountryAndName($country, 'НДС');

        $this->assertNotNull($tax);
        $this->assertNotNull($tax->getCountry());
        $this->assertInstanceOf(Country::class, $tax->getCountry());
        $this->assertEquals('НДС', $tax->getName());
        $this->assertEquals(0.2, $tax->getValue());
        $this->assertEquals('price/(1+value)*value', $tax->getExpression());
        $this->assertNotNull($tax->getCreatedAt());
    }

    public function testAlreadyCreateTax()
    {
        $taxRepositoryMock = $this->createMock(TaxRepositoryInterface::class);
        $countryRepositoryMock = $this->createMock(CountryRepositoryInterface::class);
        $service = new CreateTaxService($taxRepositoryMock, $countryRepositoryMock);

        $countryRepositoryMock->method('ofCode')
            ->willReturn(CountryFixture::getOne('Russia', 'RU'));
        $country = $countryRepositoryMock->ofCode('RU');
        $service->create($country->getCode(), 'НДС', 0.2, 'price/(1+value)*value');

        $taxRepositoryMock->method('ofCountryAndName')
            ->willReturn(TaxFixture::getOne($country, 'НДС', 0.2, 'price/(1+value)*value'));

        $this->expectException(TaxAlreadyCreatedException::class);
        $service->create($country->getCode(), 'НДС', 0.2, 'price/(1+value)*value');
    }

    public function testCreateDeletedTax()
    {
        $taxRepositoryMock = $this->createMock(TaxRepositoryInterface::class);
        $countryRepositoryMock = $this->createMock(CountryRepositoryInterface::class);
        $service = new CreateTaxService($taxRepositoryMock, $countryRepositoryMock);

        $countryRepositoryMock->method('ofCode')
            ->willReturn(CountryFixture::getOne('Russia', 'RU'));
        $country = $countryRepositoryMock->ofCode('RU');
        $service->create($country->getCode(), 'НДС', 0.2, 'price/(1+value)*value');

        $taxRepositoryMock->method('ofCountryAndNameDeleted')
            ->willReturn(TaxFixture::getOneForDeleted($country, 'НДС', 0.2, 'price/(1+value)*value'));

        $this->expectException(TaxDeletedException::class);
        $service->create($country->getCode(), 'НДС', 0.2, 'price/(1+value)*value');
    }
}
