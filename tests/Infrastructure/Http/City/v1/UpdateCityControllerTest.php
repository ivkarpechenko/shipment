<?php

namespace App\Tests\Infrastructure\Http\City\v1;

use App\Domain\City\Entity\City;
use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\HttpTestCase;
use Symfony\Component\Uid\Uuid;

class UpdateCityControllerTest extends HttpTestCase
{
    public function testUpdateCityNameRoute()
    {
        $container = $this->getContainer();

        $countryRepository = $container->get(CountryRepositoryInterface::class);
        $regionRepository = $container->get(RegionRepositoryInterface::class);
        $cityRepository = $container->get(CityRepositoryInterface::class);

        $this->assertEmpty($cityRepository->all());

        $country = CountryFixture::getOne('Russia', 'RU', Uuid::v1());
        $countryRepository->create($country);
        $country = $countryRepository->ofCode('RU');

        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $regionRepository->create($region);
        $region = $regionRepository->ofCode('MOW');

        $newCity = CityFixture::getOne($region, 'city', 'Moskva');
        $cityRepository->create($newCity);

        $this->assertEquals('Moskva', $newCity->getName());

        $this->client->request(
            'PUT',
            "/api/v1/city/{$newCity->getId()->toRfc4122()}",
            [
                'name' => 'Moskva2',
            ]
        );

        self::assertResponseIsSuccessful();

        $city = $cityRepository->ofId($newCity->getId());

        $this->assertNotNull($city);
        $this->assertInstanceOf(City::class, $city);
        $this->assertEquals('Moskva2', $city->getName());
        $this->assertEquals('city', $city->getType());
        $this->assertNotNull($city->getCreatedAt());
        $this->assertNotNull($city->getUpdatedAt());
        $this->assertNull($city->getDeletedAt());
    }

    public function testUpdateCityIsActiveRoute()
    {
        $container = $this->getContainer();

        $countryRepository = $container->get(CountryRepositoryInterface::class);
        $regionRepository = $container->get(RegionRepositoryInterface::class);
        $cityRepository = $container->get(CityRepositoryInterface::class);

        $this->assertEmpty($cityRepository->all());

        $country = CountryFixture::getOne('Russia', 'RU', Uuid::v1());
        $countryRepository->create($country);
        $country = $countryRepository->ofCode('RU');

        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $regionRepository->create($region);
        $region = $regionRepository->ofCode('MOW');

        $newCity = CityFixture::getOne($region, 'city', 'Moskva');
        $cityRepository->create($newCity);

        $this->assertTrue($newCity->isActive());

        $this->client->request(
            'PUT',
            "/api/v1/city/{$newCity->getId()->toRfc4122()}",
            [
                'isActive' => false,
            ]
        );

        self::assertResponseIsSuccessful();

        $city = $cityRepository->ofIdDeactivated($newCity->getId());

        $this->assertNotNull($city);
        $this->assertInstanceOf(City::class, $city);
        $this->assertEquals('Moskva', $city->getName());
        $this->assertEquals('city', $city->getType());
        $this->assertFalse($city->isActive());
        $this->assertNotNull($city->getCreatedAt());
        $this->assertNotNull($city->getUpdatedAt());
        $this->assertNull($city->getDeletedAt());
    }
}
