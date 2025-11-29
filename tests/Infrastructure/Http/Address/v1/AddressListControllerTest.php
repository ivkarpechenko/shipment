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

class AddressListControllerTest extends HttpTestCase
{
    public function testAll()
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

        $this->client->request('GET', '/api/v1/address');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('country', $response);
        $this->assertStringContainsString('city', $response);
        $this->assertStringContainsString('postalCode', $response);
        $this->assertStringContainsString('street', $response);
        $this->assertStringContainsString('house', $response);
        $this->assertStringContainsString('flat', $response);
        $this->assertStringContainsString('entrance', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('type', $response);
        $this->assertStringContainsString('floor', $response);
        $this->assertStringContainsString('point', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);
        $this->assertStringContainsString('deletedAt', $response);

        $this->assertStringContainsString('RU', $response);
        $this->assertStringContainsString('Russia', $response);
        $this->assertStringContainsString('city', $response);
        $this->assertStringContainsString('Moskva', $response);
        $this->assertStringContainsString('MOW', $response);
        $this->assertStringContainsString('111111', $response);
        $this->assertStringContainsString('Lenina', $response);
        $this->assertStringContainsString('1', $response);
        $this->assertStringContainsString('2', $response);
    }

    public function testPaginate()
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
            'GET',
            '/api/v1/address/paginate?page=0&offset=1'
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('country', $response);
        $this->assertStringContainsString('city', $response);
        $this->assertStringContainsString('postalCode', $response);
        $this->assertStringContainsString('street', $response);
        $this->assertStringContainsString('house', $response);
        $this->assertStringContainsString('flat', $response);
        $this->assertStringContainsString('entrance', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('type', $response);
        $this->assertStringContainsString('floor', $response);
        $this->assertStringContainsString('point', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);
        $this->assertStringContainsString('deletedAt', $response);

        $this->assertStringContainsString('RU', $response);
        $this->assertStringContainsString('Russia', $response);
        $this->assertStringContainsString('city', $response);
        $this->assertStringContainsString('Moskva', $response);
        $this->assertStringContainsString('MOW', $response);
        $this->assertStringContainsString('111111', $response);
        $this->assertStringContainsString('Lenina', $response);
        $this->assertStringContainsString('1', $response);
        $this->assertStringContainsString('2', $response);
    }
}
