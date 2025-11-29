<?php

namespace App\Tests\Domain\Address\Service;

use App\Domain\Address\Repository\AddressRepositoryInterface;
use App\Domain\Address\Service\DeleteAddressService;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\Address\PointValueFixture;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DeleteAddressServiceTest extends KernelTestCase
{
    public function testSoftDeleteAddress()
    {
        $country = CountryFixture::getOne('Russia', 'RU');
        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $city = CityFixture::getOne($region, 'city', 'Moskva');
        $oldAddress = AddressFixture::getOneFilled(
            $city,
            '111111',
            '1',
            PointValueFixture::getOne(42.3142, 43.2324)
        );

        $repositoryMock = $this->createMock(AddressRepositoryInterface::class);
        $repositoryMock->method('ofId')->willReturn($oldAddress);

        $service = new DeleteAddressService($repositoryMock);

        $this->assertNull($oldAddress->getDeletedAt());

        $service->delete($oldAddress->getId());

        $repositoryMock->method('ofId')->willReturn($oldAddress);

        $city = $repositoryMock->ofId($oldAddress->getId());

        $this->assertNotNull($city);
        $this->assertNotNull($city->getDeletedAt());
    }
}
