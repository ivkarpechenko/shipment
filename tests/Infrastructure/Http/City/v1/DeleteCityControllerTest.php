<?php

namespace App\Tests\Infrastructure\Http\City\v1;

use App\Domain\City\Entity\City;
use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Region\Entity\Region;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\HttpTestCase;
use Symfony\Component\Uid\Uuid;

class DeleteCityControllerTest extends HttpTestCase
{
    public function testDeleteCityRoute()
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

        $this->client->request(
            'DElETE',
            "/api/v1/city/{$newCity->getId()->toRfc4122()}"
        );

        self::assertResponseStatusCodeSame(204);

        $deletedCity = $cityRepository->ofIdDeleted($newCity->getId());

        $this->assertNotNull($deletedCity);
        $this->assertInstanceOf(City::class, $deletedCity);
        $this->assertInstanceOf(Region::class, $deletedCity->getRegion());
        $this->assertEquals('Moskva', $deletedCity->getName());
        $this->assertEquals('city', $deletedCity->getType());
        $this->assertNotNull($deletedCity->getCreatedAt());
        $this->assertNull($deletedCity->getUpdatedAt());
        $this->assertNotNull($deletedCity->getDeletedAt());
    }
}
