<?php

namespace App\Tests\Domain\Region\Service;

use App\Domain\Country\Entity\Country;
use App\Domain\Region\Entity\Region;
use App\Domain\Region\Exception\RegionNotFoundException;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Domain\Region\Service\UpdateRegionService;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class UpdateRegionServiceTest extends KernelTestCase
{
    private RegionRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->createMock(RegionRepositoryInterface::class);
    }

    public function testUpdateRegionName()
    {
        $country = CountryFixture::getOne('Russia', 'RU');

        $oldRegion = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $this->repository->method('ofId')->willReturn($oldRegion);

        $service = new UpdateRegionService($this->repository);

        $service->update($oldRegion->getId(), 'Moskva2', null);

        $this->repository->method('ofId')->willReturn(RegionFixture::getOne(
            $country,
            'Moskva2',
            'MOW',
            $oldRegion->getId()
        ));

        $newRegion = $this->repository->ofId($oldRegion->getId());

        $this->assertNotNull($newRegion);
        $this->assertInstanceOf(Region::class, $newRegion);
        $this->assertNotNull($newRegion->getCountry());
        $this->assertInstanceOf(Country::class, $newRegion->getCountry());
        $this->assertEquals('Moskva2', $newRegion->getName());
        $this->assertEquals('MOW', $newRegion->getCode());
        $this->assertNotNull($newRegion->getCreatedAt());
        $this->assertNotNull($newRegion->getUpdatedAt());
    }

    public function testUpdateRegionNameIfNotFound()
    {
        $this->repository->method('ofId')->willReturn(null);

        $service = new UpdateRegionService($this->repository);

        $this->expectException(RegionNotFoundException::class);
        $service->update(Uuid::v1(), 'Moskva', null);
    }

    public function testUpdateRegionIsActive()
    {
        $country = CountryFixture::getOne('Russia', 'RU');

        $oldRegion = RegionFixture::getOneForIsActive($country, 'Moskva', 'MOW', true);
        $this->repository->method('ofId')->willReturn($oldRegion);

        $service = new UpdateRegionService($this->repository);

        $this->assertTrue($oldRegion->isActive());

        $service->update($oldRegion->getId(), null, false);

        $this->repository->method('ofId')->willReturn(RegionFixture::getOneForIsActive(
            $country,
            'Moskva',
            'MOW',
            false,
            $oldRegion->getId()
        ));

        $newRegion = $this->repository->ofId($oldRegion->getId());

        $this->assertNotNull($newRegion);
        $this->assertInstanceOf(Region::class, $newRegion);
        $this->assertNotNull($newRegion->getCountry());
        $this->assertInstanceOf(Country::class, $newRegion->getCountry());
        $this->assertEquals('Moskva', $newRegion->getName());
        $this->assertEquals('MOW', $newRegion->getCode());
        $this->assertFalse($newRegion->isActive());
        $this->assertNotNull($newRegion->getCreatedAt());
        $this->assertNotNull($newRegion->getUpdatedAt());
    }

    public function testUpdateRegionIsActiveIfNotFound()
    {
        $service = new UpdateRegionService($this->repository);

        $this->expectException(RegionNotFoundException::class);
        $service->update(Uuid::v1(), 'Moskva', true);
    }
}
