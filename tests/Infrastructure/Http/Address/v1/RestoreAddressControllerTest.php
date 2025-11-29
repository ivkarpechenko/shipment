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

class RestoreAddressControllerTest extends HttpTestCase
{
    public function testRestoreAddressRoute()
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

        $address = AddressFixture::getOneForDeleted(
            $city,
            '111111',
            '1',
            PointValueFixture::getOne(42.3232, 43.2323),
            '1',
            'Lenina',
            '1'
        );
        $addressRepository->create($address);

        $this->client->request(
            'POST',
            "/api/v1/address/{$address->getId()->toRfc4122()}/restore"
        );

        self::assertResponseIsSuccessful();

        $restoredAddress = $addressRepository->ofId($address->getId());

        $this->assertNotNull($restoredAddress);
        $this->assertNull($restoredAddress->getDeletedAt());
        $this->assertNotNull($restoredAddress->getUpdatedAt());
    }
}
