<?php

namespace App\Tests\Application\Address\Query;

use App\Application\Address\Query\FindAddressesByCityQuery;
use App\Application\Address\Query\FindAddressesByCityQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Domain\Address\Entity\Address;
use App\Domain\Address\Repository\AddressRepositoryInterface;
use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\Address\PointValueFixture;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class FindAddressesByCityQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $country = CountryFixture::getOne('Russia', 'RU');
        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $city = CityFixture::getOne($region, 'city', 'Moskva');
        $this->assertInstanceOf(
            Query::class,
            new FindAddressesByCityQuery($city)
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindAddressesByCityQueryHandler::class)
        );
    }

    public function testFindCitysByCountryQueryHandler()
    {
        $container = $this->getContainer();

        $countryRepository = $container->get(CountryRepositoryInterface::class);
        $regionRepository = $container->get(RegionRepositoryInterface::class);
        $cityRepository = $container->get(CityRepositoryInterface::class);
        $addressRepository = $container->get(AddressRepositoryInterface::class);

        $this->assertEmpty($cityRepository->all());

        $country = CountryFixture::getOne('Russia', 'RU', Uuid::v1());
        $countryRepository->create($country);
        $country = $countryRepository->ofCode('RU');

        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $regionRepository->create($region);
        $region = $regionRepository->ofCode('MOW');

        $city = CityFixture::getOne($region, 'city', 'Moskva');
        $cityRepository->create($city);
        $city = $cityRepository->ofId($city->getId());

        $newAddress = AddressFixture::getOneFilled(
            $city,
            '111111',
            '1',
            PointValueFixture::getOne(42.4242, 43.2425)
        );
        $addressRepository->create($newAddress);

        $addresses = $container->get(FindAddressesByCityQueryHandler::class)(
            new FindAddressesByCityQuery($city)
        );

        $this->assertIsArray($addresses);

        $address = $addresses[0];
        $this->assertNotNull($address);
        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals($newAddress->getId(), $address->getId());
        $this->assertEquals($newAddress->getStreet(), $address->getStreet());
        $this->assertEquals($newAddress->getFlat(), $address->getFlat());
        $this->assertEquals($newAddress->getPostalCode(), $address->getPostalCode());
        $this->assertEquals($newAddress->getHouse(), $address->getHouse());
        $this->assertEquals($newAddress->getEntrance(), $address->getEntrance());
        $this->assertEquals($newAddress->getFloor(), $address->getFloor());
        $this->assertEquals($newAddress->getPoint(), $address->getPoint());
        $this->assertTrue($address->isActive());
        $this->assertNotNull($address->getCreatedAt());
        $this->assertNull($address->getUpdatedAt());
        $this->assertNull($address->getDeletedAt());
    }
}
