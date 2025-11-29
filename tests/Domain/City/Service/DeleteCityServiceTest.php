<?php

namespace App\Tests\Domain\City\Service;

use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\City\Service\DeleteCityService;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DeleteCityServiceTest extends KernelTestCase
{
    public function testSoftDeleteCity()
    {
        $country = CountryFixture::getOne('Russia', 'RU');
        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');

        $oldCity = CityFixture::getOneForDeleted($region, 'city', 'Moskva', false);

        $repositoryMock = $this->createMock(CityRepositoryInterface::class);
        $repositoryMock->method('ofId')->willReturn($oldCity);

        $service = new DeleteCityService($repositoryMock);

        $this->assertNull($oldCity->getDeletedAt());

        $service->delete($oldCity->getId());

        $repositoryMock->method('ofId')->willReturn($oldCity);

        $city = $repositoryMock->ofId($oldCity->getId());

        $this->assertNotNull($city);
        $this->assertNotNull($city->getDeletedAt());
    }
}
