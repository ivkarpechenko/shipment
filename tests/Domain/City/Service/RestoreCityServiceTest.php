<?php

namespace App\Tests\Domain\City\Service;

use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\City\Service\RestoreCityService;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RestoreCityServiceTest extends KernelTestCase
{
    public function testRestoreCity()
    {
        $country = CountryFixture::getOne('Russia', 'RU');
        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $oldCity = CityFixture::getOneForDeleted($region, 'city', 'Moskva');

        $repositoryMock = $this->createMock(CityRepositoryInterface::class);
        $repositoryMock->method('ofIdDeleted')->willReturn($oldCity);

        $this->assertNotNull($oldCity->getDeletedAt());

        $service = new RestoreCityService($repositoryMock);

        $service->restore($oldCity->getId());

        $this->assertNull($oldCity->getDeletedAt());

        $repositoryMock->method('ofId')->willReturn($oldCity);

        $city = $repositoryMock->ofId($oldCity->getId());

        $this->assertNotNull($city);
        $this->assertNull($city->getDeletedAt());
    }
}
