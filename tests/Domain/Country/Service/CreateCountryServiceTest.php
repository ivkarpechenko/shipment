<?php

namespace App\Tests\Domain\Country\Service;

use App\Domain\Country\Entity\Country;
use App\Domain\Country\Exception\CountryAlreadyCreatedException;
use App\Domain\Country\Exception\CountryDeactivatedException;
use App\Domain\Country\Exception\CountryDeletedException;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Country\Service\CreateCountryService;
use App\Tests\Fixture\Country\CountryFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateCountryServiceTest extends KernelTestCase
{
    public function testCreateCountry()
    {
        $repositoryMock = $this->createMock(CountryRepositoryInterface::class);
        $service = new CreateCountryService($repositoryMock);

        $service->create('test country', 'RU');

        $repositoryMock->method('ofCode')
            ->willReturn(CountryFixture::getOne('test country', 'RU'));

        $country = $repositoryMock->ofCode('RU');

        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals('test country', $country->getName());
        $this->assertEquals('RU', $country->getCode());
        $this->assertNotNull($country->getCreatedAt());
        $this->assertNull($country->getUpdatedAt());
    }

    public function testAlreadyCreateCountry()
    {
        $repositoryMock = $this->createMock(CountryRepositoryInterface::class);
        $service = new CreateCountryService($repositoryMock);

        $repositoryMock->method('ofCode')
            ->willReturn(CountryFixture::getOne('test country', 'RU'));

        $this->expectException(CountryAlreadyCreatedException::class);
        $service->create('test country', 'RU');
    }

    public function testCreateDeactivatedCountry()
    {
        $repositoryMock = $this->createMock(CountryRepositoryInterface::class);
        $service = new CreateCountryService($repositoryMock);

        $repositoryMock->method('ofCodeDeactivated')
            ->willReturn(CountryFixture::getOneForIsActive('test country', 'RU', false));

        $this->expectException(CountryDeactivatedException::class);
        $service->create('test country', 'RU');
    }

    public function testCreateDeletedCountry()
    {
        $repositoryMock = $this->createMock(CountryRepositoryInterface::class);
        $service = new CreateCountryService($repositoryMock);

        $repositoryMock->method('ofCodeDeleted')
            ->willReturn(CountryFixture::getOneForDeleted('test country', 'RU'));

        $this->expectException(CountryDeletedException::class);
        $service->create('test country', 'RU');
    }
}
