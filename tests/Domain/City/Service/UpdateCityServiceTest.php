<?php

namespace App\Tests\Domain\City\Service;

use App\Domain\City\Entity\City;
use App\Domain\City\Exception\CityNotFoundException;
use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\City\Service\UpdateCityService;
use App\Domain\Region\Entity\Region;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class UpdateCityServiceTest extends KernelTestCase
{
    private CityRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->createMock(CityRepositoryInterface::class);
    }

    public function testUpdateCityName()
    {
        $country = CountryFixture::getOne('Russia', 'RU');
        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $oldCity = CityFixture::getOne($region, 'city', 'Moskva');

        $this->repository->method('ofId')->willReturn($oldCity);

        $service = new UpdateCityService($this->repository);

        $service->update($oldCity->getId(), 'village', 'Moskva2', null);

        $this->repository->method('ofId')->willReturn(CityFixture::getOne(
            $region,
            'village',
            'Moskva2',
            $oldCity->getId()
        ));

        $newCity = $this->repository->ofId($oldCity->getId());

        $this->assertNotNull($newCity);
        $this->assertInstanceOf(City::class, $newCity);
        $this->assertNotNull($newCity->getRegion());
        $this->assertInstanceOf(Region::class, $newCity->getRegion());
        $this->assertEquals('Moskva2', $newCity->getName());
        $this->assertEquals('village', $newCity->getType());
        $this->assertNotNull($newCity->getCreatedAt());
        $this->assertNotNull($newCity->getUpdatedAt());
    }

    public function testUpdateCityNameIfNotFound()
    {
        $this->repository->method('ofId')->willReturn(null);

        $service = new UpdateCityService($this->repository);

        $this->expectException(CityNotFoundException::class);
        $service->update(Uuid::v1(), 'city', 'Moskva', null);
    }

    public function testUpdateCityIsActive()
    {
        $country = CountryFixture::getOne('Russia', 'RU');
        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $oldCity = CityFixture::getOneForIsActive($region, 'city', 'Moskva');

        $this->repository->method('ofId')->willReturn($oldCity);

        $service = new UpdateCityService($this->repository);

        $this->assertTrue($oldCity->isActive());

        $service->update($oldCity->getId(), null, null, false);

        $this->repository->method('ofId')->willReturn(CityFixture::getOneForIsActive(
            $region,
            'city',
            'Moskva',
            false,
            $oldCity->getId()
        ));

        $newCity = $this->repository->ofId($oldCity->getId());

        $this->assertNotNull($newCity);
        $this->assertInstanceOf(City::class, $newCity);
        $this->assertNotNull($newCity->getRegion());
        $this->assertInstanceOf(Region::class, $newCity->getRegion());
        $this->assertEquals('Moskva', $newCity->getName());
        $this->assertEquals('city', $newCity->getType());
        $this->assertNotNull($newCity->getCreatedAt());
        $this->assertNotNull($newCity->getUpdatedAt());
    }

    public function testUpdateCityIsActiveIfNotFound()
    {
        $service = new UpdateCityService($this->repository);

        $this->expectException(CityNotFoundException::class);
        $service->update(Uuid::v1(), 'city', 'Moskva', true);
    }
}
