<?php

namespace App\Tests\Infrastructure\DBAL\Repository\Doctrine\City;

use App\Domain\City\Entity\City;
use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Tests\DoctrineTestCase;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use Symfony\Component\Uid\Uuid;

class DoctrineCityRepositoryTest extends DoctrineTestCase
{
    public function testCreateCity()
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

        $city = $cityRepository->ofId($newCity->getId());

        $this->assertNotNull($city);
        $this->assertInstanceOf(City::class, $city);
        $this->assertEquals($newCity->getId(), $city->getId());
        $this->assertEquals($newCity->getName(), $city->getName());
        $this->assertEquals($newCity->getType(), $city->getType());
    }

    public function testUpdateCity()
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

        $city = $cityRepository->ofId($newCity->getId());

        $city->changeName('Moskva2');
        $cityRepository->update($city);

        $updatedCity = $cityRepository->ofId($newCity->getId());

        $this->assertNotNull($updatedCity->getUpdatedAt());
        $this->assertEquals('Moskva2', $updatedCity->getName());

        $this->assertTrue($updatedCity->isActive());

        $updatedCity->changeIsActive(false);
        $cityRepository->update($updatedCity);

        $deactivatedCity = $cityRepository->ofIdDeactivated($updatedCity->getId());

        $this->assertNotNull($deactivatedCity);
        $this->assertFalse($deactivatedCity->isActive());
    }

    public function testDeleteCity()
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

        $createdCity = $cityRepository->ofId($newCity->getId());
        $this->assertNotNull($createdCity);
        $this->assertInstanceOf(City::class, $createdCity);
        $this->assertNull($createdCity->getDeletedAt());

        $createdCity->deleted();
        $cityRepository->delete($createdCity);

        $deletedCity = $cityRepository->ofIdDeleted($createdCity->getId());

        $this->assertNotNull($deletedCity);
        $this->assertNotNull($deletedCity->getDeletedAt());
    }

    public function testRestoreCity()
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
        $newCity->deleted();
        $cityRepository->create($newCity);

        $deletedCity = $cityRepository->ofIdDeleted($newCity->getId());
        $this->assertNotNull($deletedCity);
        $this->assertInstanceOf(City::class, $deletedCity);
        $this->assertNotNull($deletedCity->getDeletedAt());

        $deletedCity->restored();
        $cityRepository->restore($deletedCity);

        $restoredCity = $cityRepository->ofId($deletedCity->getId());

        $this->assertNotNull($restoredCity);
        $this->assertNull($restoredCity->getDeletedAt());
        $this->assertNotNull($restoredCity->getUpdatedAt());
    }

    public function testOfId()
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

        $city = $cityRepository->ofId($newCity->getId());

        $this->assertNotNull($city);
        $this->assertInstanceOf(City::class, $city);
        $this->assertEquals($newCity->getId(), $city->getId());
        $this->assertEquals($newCity->getName(), $city->getName());
        $this->assertEquals($newCity->getType(), $city->getType());
        $this->assertTrue($city->isActive());
        $this->assertNotNull($city->getCreatedAt());
        $this->assertNull($city->getUpdatedAt());
        $this->assertNull($city->getDeletedAt());
    }

    public function testOfTypeAndName()
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

        $city = $cityRepository->ofTypeAndName('city', 'Moskva');

        $this->assertNotNull($city);
        $this->assertInstanceOf(City::class, $city);
        $this->assertEquals($newCity->getId(), $city->getId());
        $this->assertEquals($newCity->getName(), $city->getName());
        $this->assertEquals($newCity->getType(), $city->getType());
        $this->assertTrue($city->isActive());
        $this->assertNotNull($city->getCreatedAt());
        $this->assertNull($city->getUpdatedAt());
        $this->assertNull($city->getDeletedAt());
    }

    public function testOfType()
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

        $cities = $cityRepository->ofType($newCity->getType());

        $this->assertNotEmpty($cities);
        $this->assertIsArray($cities);

        $city = reset($cities);

        $this->assertNotNull($city);
        $this->assertInstanceOf(City::class, $city);
        $this->assertEquals($newCity->getId(), $city->getId());
        $this->assertEquals($newCity->getName(), $city->getName());
        $this->assertEquals($newCity->getType(), $city->getType());
        $this->assertTrue($city->isActive());
        $this->assertNotNull($city->getCreatedAt());
        $this->assertNull($city->getUpdatedAt());
        $this->assertNull($city->getDeletedAt());
    }

    public function testOfIdDeleted()
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
        $newCity->deleted();
        $cityRepository->create($newCity);

        $deletedCity = $cityRepository->ofIdDeleted($newCity->getId());

        $this->assertNotNull($deletedCity);
        $this->assertInstanceOf(City::class, $deletedCity);
        $this->assertEquals($newCity->getId(), $deletedCity->getId());
        $this->assertEquals($newCity->getName(), $deletedCity->getName());
        $this->assertEquals($newCity->getType(), $deletedCity->getType());
        $this->assertTrue($deletedCity->isActive());
        $this->assertNotNull($deletedCity->getCreatedAt());
        $this->assertNull($deletedCity->getUpdatedAt());
        $this->assertNotNull($deletedCity->getDeletedAt());
    }

    public function testOfIdDeactivated()
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
        $newCity->changeIsActive(false);
        $cityRepository->create($newCity);

        $deactivatedCity = $cityRepository->ofIdDeactivated($newCity->getId());

        $this->assertNotNull($deactivatedCity);
        $this->assertInstanceOf(City::class, $deactivatedCity);
        $this->assertEquals($newCity->getId(), $deactivatedCity->getId());
        $this->assertEquals($newCity->getName(), $deactivatedCity->getName());
        $this->assertEquals($newCity->getType(), $deactivatedCity->getType());
        $this->assertFalse($deactivatedCity->isActive());
        $this->assertNotNull($deactivatedCity->getCreatedAt());
        $this->assertNotNull($deactivatedCity->getUpdatedAt());
        $this->assertNull($deactivatedCity->getDeletedAt());
    }

    public function testOfTypeAndNameDeleted()
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
        $newCity->deleted();
        $cityRepository->create($newCity);

        $deletedCity = $cityRepository->ofTypeAndNameDeleted($newCity->getType(), $newCity->getName());

        $this->assertNotNull($deletedCity);
        $this->assertInstanceOf(City::class, $deletedCity);
        $this->assertEquals($newCity->getId(), $deletedCity->getId());
        $this->assertEquals($newCity->getName(), $deletedCity->getName());
        $this->assertEquals($newCity->getType(), $deletedCity->getType());
        $this->assertTrue($deletedCity->isActive());
        $this->assertNotNull($deletedCity->getCreatedAt());
        $this->assertNull($deletedCity->getUpdatedAt());
        $this->assertNotNull($deletedCity->getDeletedAt());
    }

    public function testOfTypeAndNameDeactivated()
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
        $newCity->changeIsActive(false);
        $cityRepository->create($newCity);

        $deactivatedCity = $cityRepository->ofTypeAndNameDeactivated($newCity->getType(), $newCity->getName());

        $this->assertNotNull($deactivatedCity);
        $this->assertInstanceOf(City::class, $deactivatedCity);
        $this->assertEquals($newCity->getId(), $deactivatedCity->getId());
        $this->assertEquals($newCity->getName(), $deactivatedCity->getName());
        $this->assertEquals($newCity->getType(), $deactivatedCity->getType());
        $this->assertFalse($deactivatedCity->isActive());
        $this->assertNotNull($deactivatedCity->getCreatedAt());
        $this->assertNotNull($deactivatedCity->getUpdatedAt());
        $this->assertNull($deactivatedCity->getDeletedAt());
    }
}
