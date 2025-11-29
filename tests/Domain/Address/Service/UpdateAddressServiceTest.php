<?php

namespace App\Tests\Domain\Address\Service;

use App\Domain\Address\Exception\AddressNotFoundException;
use App\Domain\Address\Repository\AddressRepositoryInterface;
use App\Domain\Address\Service\UpdateAddressService;
use App\Domain\City\Entity\City;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\DaData\DaDataAddressDtoFixture;
use App\Tests\Fixture\Region\RegionFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UpdateAddressServiceTest extends KernelTestCase
{
    private AddressRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->createMock(AddressRepositoryInterface::class);
    }

    public function testUpdateAddressIsActive()
    {
        $country = CountryFixture::getOne('Russia', 'RU');
        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $city = CityFixture::getOne($region, 'city', 'Moskva');

        $oldAddress = AddressFixture::getOneFromAddressDto($city, DaDataAddressDtoFixture::getOne());

        $this->repository->method('ofId')->willReturn($oldAddress);

        $service = new UpdateAddressService($this->repository);

        $this->assertTrue($oldAddress->isActive());

        $service->update($oldAddress->getId(), false);

        $oldAddress->changeIsActive(false);
        $this->repository->method('ofId')->willReturn($oldAddress);

        $newAddress = $this->repository->ofId($oldAddress->getId());

        $this->assertNotNull($newAddress->getCity());
        $this->assertInstanceOf(City::class, $newAddress->getCity());
        $this->assertEquals('309850', $newAddress->getPostalCode());
        $this->assertEquals('ул Слободская', $newAddress->getStreet());
        $this->assertEquals('1/1', $newAddress->getHouse());
        $this->assertFalse($newAddress->isActive());
        $this->assertNotNull($newAddress->getCreatedAt());
        $this->assertNotNull($newAddress->getUpdatedAt());
    }

    public function testUpdateAddressIsActiveIfNotFound()
    {
        $service = new UpdateAddressService($this->repository);

        $country = CountryFixture::getOne('Russia', 'RU');
        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $city = CityFixture::getOne($region, 'city', 'Moskva');

        $oldAddress = AddressFixture::getOneFromAddressDto($city, DaDataAddressDtoFixture::getOne());

        $this->expectException(AddressNotFoundException::class);

        $service->update($oldAddress->getId(), false);
    }
}
