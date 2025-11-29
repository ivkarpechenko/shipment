<?php

namespace App\Tests\Domain\Region\Service;

use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Region\Entity\Region;
use App\Domain\Region\Exception\RegionAlreadyCreatedException;
use App\Domain\Region\Exception\RegionDeactivatedException;
use App\Domain\Region\Exception\RegionDeletedException;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Domain\Region\Service\CreateRegionService;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateRegionServiceTest extends KernelTestCase
{
    public function testCreateRegion()
    {
        $regionRepositoryMock = $this->createMock(RegionRepositoryInterface::class);
        $countryRepositoryMock = $this->createMock(CountryRepositoryInterface::class);
        $service = new CreateRegionService($regionRepositoryMock, $countryRepositoryMock);

        $countryRepositoryMock->method('ofCode')
            ->willReturn(CountryFixture::getOne('Russia', 'RU'));
        $country = $countryRepositoryMock->ofCode('RU');
        $service->create($country->getCode(), 'Moskva', 'MOW');

        $regionRepositoryMock->method('ofCode')
            ->willReturn(RegionFixture::getOne($country, 'Moskva', 'MOW'));

        $region = $regionRepositoryMock->ofCode('MOW');

        $this->assertNotNull($region);
        $this->assertInstanceOf(Region::class, $region);
        $this->assertInstanceOf(Country::class, $region->getCountry());
        $this->assertEquals('Moskva', $region->getName());
        $this->assertEquals('MOW', $region->getCode());
        $this->assertNotNull($region->getCreatedAt());
        $this->assertNull($region->getUpdatedAt());
    }

    public function testAlreadyCreateRegion()
    {
        $regionRepositoryMock = $this->createMock(RegionRepositoryInterface::class);
        $countryRepositoryMock = $this->createMock(CountryRepositoryInterface::class);
        $service = new CreateRegionService($regionRepositoryMock, $countryRepositoryMock);

        $countryRepositoryMock->method('ofCode')
            ->willReturn(CountryFixture::getOne('Russia', 'RU'));
        $country = $countryRepositoryMock->ofCode('RU');
        $service->create($country->getCode(), 'Moskva', 'MOW');

        $regionRepositoryMock->method('ofCode')
            ->willReturn(RegionFixture::getOne($country, 'Moskva', 'MOW'));

        $this->expectException(RegionAlreadyCreatedException::class);
        $service->create($country->getCode(), 'Moskva', 'MOW');
    }

    public function testCreateDeactivatedRegion()
    {
        $regionRepositoryMock = $this->createMock(RegionRepositoryInterface::class);
        $countryRepositoryMock = $this->createMock(CountryRepositoryInterface::class);
        $service = new CreateRegionService($regionRepositoryMock, $countryRepositoryMock);

        $countryRepositoryMock->method('ofCode')
            ->willReturn(CountryFixture::getOne('Russia', 'RU'));
        $country = $countryRepositoryMock->ofCode('RU');
        $service->create($country->getCode(), 'Moskva', 'MOW');

        $regionRepositoryMock->method('ofCodeDeactivated')
            ->willReturn(RegionFixture::getOneForIsActive($country, 'Moskva', 'MOW', false));

        $this->expectException(RegionDeactivatedException::class);
        $service->create($country->getCode(), 'Moskva', 'MOW');
    }

    public function testCreateDeletedRegion()
    {
        $regionRepositoryMock = $this->createMock(RegionRepositoryInterface::class);
        $countryRepositoryMock = $this->createMock(CountryRepositoryInterface::class);
        $service = new CreateRegionService($regionRepositoryMock, $countryRepositoryMock);

        $countryRepositoryMock->method('ofCode')
            ->willReturn(CountryFixture::getOne('Russia', 'RU'));
        $country = $countryRepositoryMock->ofCode('RU');
        $service->create($country->getCode(), 'Moskva', 'MOW');

        $regionRepositoryMock->method('ofCodeDeleted')
            ->willReturn(RegionFixture::getOneForDeleted($country, 'Moskva', 'MOW'));

        $this->expectException(RegionDeletedException::class);
        $service->create($country->getCode(), 'Moskva', 'MOW');
    }
}
