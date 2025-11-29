<?php

namespace App\Tests\Domain\Address\Service;

use App\Domain\Address\Repository\AddressRepositoryInterface;
use App\Domain\Address\Service\RestoreAddressService;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\Address\PointValueFixture;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RestoreAddressServiceTest extends KernelTestCase
{
    public function testRestoreAddress()
    {
        $country = CountryFixture::getOne('Russia', 'RU');
        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $city = CityFixture::getOne($region, 'city', 'Moskva');
        $oldAddress = AddressFixture::getOneForDeleted(
            $city,
            '111111',
            '1',
            PointValueFixture::getOne(42.4234, 43.2424)
        );

        $repositoryMock = $this->createMock(AddressRepositoryInterface::class);
        $repositoryMock->method('ofIdDeleted')->willReturn($oldAddress);

        $this->assertNotNull($oldAddress->getDeletedAt());

        $service = new RestoreAddressService($repositoryMock);

        $service->restore($oldAddress->getId());

        $this->assertNull($oldAddress->getDeletedAt());

        $repositoryMock->method('ofId')->willReturn($oldAddress);

        $city = $repositoryMock->ofId($oldAddress->getId());

        $this->assertNotNull($city);
        $this->assertNull($city->getDeletedAt());
    }
}
