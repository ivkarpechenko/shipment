<?php

namespace App\Tests\Domain\City\Service;

use App\Domain\City\Entity\City;
use App\Domain\City\Exception\CityAlreadyCreatedException;
use App\Domain\City\Exception\CityDeactivatedException;
use App\Domain\City\Exception\CityDeletedException;
use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\City\Service\CreateCityService;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Region\Entity\Region;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Domain\Region\Service\CreateRegionService;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateCityServiceTest extends KernelTestCase
{
    public function testCreateCity()
    {
        $countryRepositoryMock = $this->createMock(CountryRepositoryInterface::class);
        $regionRepositoryMock = $this->createMock(RegionRepositoryInterface::class);
        $cityRepositoryMock = $this->createMock(CityRepositoryInterface::class);

        $regionService = new CreateRegionService($regionRepositoryMock, $countryRepositoryMock);
        $cityService = new CreateCityService($cityRepositoryMock, $regionRepositoryMock);

        $countryRepositoryMock->method('ofCode')
            ->willReturn(CountryFixture::getOne('Russia', 'RU'));
        $country = $countryRepositoryMock->ofCode('RU');
        $regionService->create($country->getCode(), 'Moskva', 'MOW');

        $regionRepositoryMock->method('ofCode')
            ->willReturn(RegionFixture::getOne($country, 'Moskva', 'MOW'));
        $region = $regionRepositoryMock->ofCode('MOW');
        $cityService->create($region->getCode(), 'Moskva', 'MOW');

        $cityRepositoryMock->method('ofTypeAndName')
            ->willReturn(CityFixture::getOne($region, 'city', 'Moskva'));

        $city = $cityRepositoryMock->ofTypeAndName('city', 'Moskva');

        $this->assertNotNull($city);
        $this->assertInstanceOf(City::class, $city);
        $this->assertInstanceOf(Region::class, $city->getRegion());
        $this->assertEquals('Moskva', $city->getName());
        $this->assertEquals('city', $city->getType());
        $this->assertNotNull($city->getCreatedAt());
        $this->assertNull($city->getUpdatedAt());
    }

    public function testAlreadyCreateCity()
    {
        $countryRepositoryMock = $this->createMock(CountryRepositoryInterface::class);
        $regionRepositoryMock = $this->createMock(RegionRepositoryInterface::class);
        $cityRepositoryMock = $this->createMock(CityRepositoryInterface::class);

        $regionService = new CreateRegionService($regionRepositoryMock, $countryRepositoryMock);
        $cityService = new CreateCityService($cityRepositoryMock, $regionRepositoryMock);

        $countryRepositoryMock->method('ofCode')
            ->willReturn(CountryFixture::getOne('Russia', 'RU'));
        $country = $countryRepositoryMock->ofCode('RU');
        $regionService->create($country->getCode(), 'Moskva', 'MOW');

        $regionRepositoryMock->method('ofCode')
            ->willReturn(RegionFixture::getOne($country, 'Moskva', 'MOW'));
        $region = $regionRepositoryMock->ofCode('MOW');
        $cityService->create($region->getCode(), 'Moskva', 'MOW');

        $cityRepositoryMock->method('ofTypeAndName')
            ->willReturn(CityFixture::getOne($region, 'city', 'Moskva'));

        $this->expectException(CityAlreadyCreatedException::class);
        $cityService->create($region->getCode(), 'city', 'Moskva');
    }

    public function testCreateDeactivatedCity()
    {
        $countryRepositoryMock = $this->createMock(CountryRepositoryInterface::class);
        $regionRepositoryMock = $this->createMock(RegionRepositoryInterface::class);
        $cityRepositoryMock = $this->createMock(CityRepositoryInterface::class);

        $regionService = new CreateRegionService($regionRepositoryMock, $countryRepositoryMock);
        $cityService = new CreateCityService($cityRepositoryMock, $regionRepositoryMock);

        $countryRepositoryMock->method('ofCode')
            ->willReturn(CountryFixture::getOne('Russia', 'RU'));
        $country = $countryRepositoryMock->ofCode('RU');
        $regionService->create($country->getCode(), 'Moskva', 'MOW');

        $regionRepositoryMock->method('ofCode')
            ->willReturn(RegionFixture::getOne($country, 'Moskva', 'MOW'));
        $region = $regionRepositoryMock->ofCode('MOW');
        $cityService->create($region->getCode(), 'Moskva', 'MOW');

        $cityRepositoryMock->method('ofTypeAndNameDeactivated')
            ->willReturn(CityFixture::getOneForIsActive($region, 'city', 'Moskva', false));

        $this->expectException(CityDeactivatedException::class);
        $cityService->create($region->getCode(), 'city', 'Moskva');
    }

    public function testCreateDeletedCity()
    {
        $countryRepositoryMock = $this->createMock(CountryRepositoryInterface::class);
        $regionRepositoryMock = $this->createMock(RegionRepositoryInterface::class);
        $cityRepositoryMock = $this->createMock(CityRepositoryInterface::class);

        $regionService = new CreateRegionService($regionRepositoryMock, $countryRepositoryMock);
        $cityService = new CreateCityService($cityRepositoryMock, $regionRepositoryMock);

        $countryRepositoryMock->method('ofCode')
            ->willReturn(CountryFixture::getOne('Russia', 'RU'));
        $country = $countryRepositoryMock->ofCode('RU');
        $regionService->create($country->getCode(), 'Moskva', 'MOW');

        $regionRepositoryMock->method('ofCode')
            ->willReturn(RegionFixture::getOne($country, 'Moskva', 'MOW'));
        $region = $regionRepositoryMock->ofCode('MOW');
        $cityService->create($region->getCode(), 'Moskva', 'MOW');

        $cityRepositoryMock->method('ofTypeAndNameDeleted')
            ->willReturn(CityFixture::getOneForDeleted($region, 'city', 'Moskva'));

        $this->expectException(CityDeletedException::class);
        $cityService->create($region->getCode(), 'city', 'Moskva');
    }
}
