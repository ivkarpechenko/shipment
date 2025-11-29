<?php

namespace App\Tests\Application\Address\Query;

use App\Application\Address\Query\External\FindExternalAddressInterface;
use App\Application\Address\Query\FindAddressByAddressQuery;
use App\Application\Address\Query\FindAddressByAddressQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Domain\Address\Entity\Address;
use App\Domain\Address\Repository\AddressRepositoryInterface;
use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\DaData\DaDataAddressDtoFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class FindAddressByAddressDtoQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindAddressByAddressQuery('309850, Белгородская обл, Алексеевский р-н, г Алексеевка, ул Слободская, д 1/1')
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindAddressByAddressQueryHandler::class)
        );
    }

    public function testFindAddressByAddressDtoQueryHandler()
    {
        $container = $this->getContainer();

        $countryRepository = $container->get(CountryRepositoryInterface::class);
        $regionRepository = $container->get(RegionRepositoryInterface::class);
        $cityRepository = $container->get(CityRepositoryInterface::class);
        $addressRepository = $container->get(AddressRepositoryInterface::class);

        $country = CountryFixture::getOne('Russia', 'RU', Uuid::v1());
        $countryRepository->create($country);
        $country = $countryRepository->ofCode('RU');

        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $regionRepository->create($region);
        $region = $regionRepository->ofCode('MOW');

        $city = CityFixture::getOne($region, 'city', 'Moskva');
        $cityRepository->create($city);
        $city = $cityRepository->ofId($city->getId());

        $newAddress = AddressFixture::getOneFromAddressDto($city, DaDataAddressDtoFixture::getOne());
        $addressRepository->create($newAddress);

        $address = $container->get(FindAddressByAddressQueryHandler::class)(
            new FindAddressByAddressQuery('309850, Белгородская обл, Алексеевский р-н, г Алексеевка, ул Слободская, д 1/1')
        );

        $this->assertNotNull($address);
        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals($newAddress->getId(), $address->getId());
        $this->assertEquals($newAddress->getStreet(), $address->getStreet());
        $this->assertEquals($newAddress->getFlat(), $address->getFlat());
        $this->assertEquals($newAddress->getPostalCode(), $address->getPostalCode());
        $this->assertEquals($newAddress->getHouse(), $address->getHouse());
        $this->assertEquals($newAddress->getEntrance(), $address->getEntrance());
        $this->assertEquals($newAddress->getFloor(), $address->getFloor());
        $this->assertTrue($address->isActive());
        $this->assertNotNull($address->getCreatedAt());
        $this->assertNull($address->getUpdatedAt());
        $this->assertNull($address->getDeletedAt());
    }

    public function testFindAddressByAddressDtoQueryHandlerFromDadata()
    {
        $container = $this->getContainer();

        $countryRepository = $container->get(CountryRepositoryInterface::class);
        $regionRepository = $container->get(RegionRepositoryInterface::class);
        $cityRepository = $container->get(CityRepositoryInterface::class);
        $addressRepository = $container->get(AddressRepositoryInterface::class);

        $country = CountryFixture::getOne('Russia', 'RU', Uuid::v1());
        $countryRepository->create($country);
        $country = $countryRepository->ofCode('RU');

        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $regionRepository->create($region);
        $region = $regionRepository->ofCode('MOW');

        $city = CityFixture::getOne($region, 'city', 'Moskva');
        $cityRepository->create($city);
        $city = $cityRepository->ofId($city->getId());

        $newAddress = AddressFixture::getOneFromAddressDto($city, DaDataAddressDtoFixture::getOne());
        $addressRepository->create($newAddress);

        $service = $this->createMock(FindExternalAddressInterface::class);
        $service->method('find')->willReturn(DaDataAddressDtoFixture::getOne());
        $container->set(FindExternalAddressInterface::class, $service);

        $address = $container->get(FindAddressByAddressQueryHandler::class)(
            new FindAddressByAddressQuery('Белгородская, Алексеевский, Алексеевка, Слободская, 1/1')
        );

        $this->assertNotNull($address);
        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals($newAddress->getId(), $address->getId());
        $this->assertEquals($newAddress->getStreet(), $address->getStreet());
        $this->assertEquals($newAddress->getFlat(), $address->getFlat());
        $this->assertEquals($newAddress->getPostalCode(), $address->getPostalCode());
        $this->assertEquals($newAddress->getHouse(), $address->getHouse());
        $this->assertEquals($newAddress->getEntrance(), $address->getEntrance());
        $this->assertEquals($newAddress->getFloor(), $address->getFloor());
        $this->assertTrue($address->isActive());
        $this->assertNotNull($address->getCreatedAt());
        $this->assertNull($address->getUpdatedAt());
        $this->assertNull($address->getDeletedAt());
    }
}
