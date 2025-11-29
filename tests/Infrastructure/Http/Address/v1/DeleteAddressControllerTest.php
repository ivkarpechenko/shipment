<?php

namespace App\Tests\Infrastructure\Http\Address\v1;

use App\Domain\Address\Repository\AddressRepositoryInterface;
use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\Address\PointValueFixture;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\HttpTestCase;
use Symfony\Component\Uid\Uuid;

class DeleteAddressControllerTest extends HttpTestCase
{
    public function testDeleteAddressRoute()
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

        $address = AddressFixture::getOneFilled(
            $city,
            '111111, Lenina, 1, 1',
            '1',
            PointValueFixture::getOne(42.4234, 43.2425),
            '111111',
            'Lenina',
            '2'
        );
        $addressRepository->create($address);

        $this->client->request(
            'DElETE',
            "/api/v1/address/{$address->getId()->toRfc4122()}"
        );

        self::assertResponseStatusCodeSame(204);

        $deletedAddress = $addressRepository->ofIdDeleted($address->getId());

        $this->assertNotNull($deletedAddress);
        $this->assertNotNull($deletedAddress->getDeletedAt());
    }
}
