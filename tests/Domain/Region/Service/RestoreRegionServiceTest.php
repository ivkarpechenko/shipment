<?php

namespace App\Tests\Domain\Region\Service;

use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Domain\Region\Service\RestoreRegionService;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RestoreRegionServiceTest extends KernelTestCase
{
    public function testRestoreRegion()
    {
        $country = CountryFixture::getOne('Russia', 'RU');
        $oldRegion = RegionFixture::getOneForDeleted($country, 'Moskva', 'MOW');
        $repositoryMock = $this->createMock(RegionRepositoryInterface::class);
        $repositoryMock->method('ofIdDeleted')->willReturn($oldRegion);

        $this->assertNotNull($oldRegion->getDeletedAt());

        $service = new RestoreRegionService($repositoryMock);

        $service->restore($oldRegion->getId());

        $this->assertNull($oldRegion->getDeletedAt());

        $repositoryMock->method('ofId')->willReturn($oldRegion);

        $region = $repositoryMock->ofId($oldRegion->getId());

        $this->assertNotNull($region);
        $this->assertNull($region->getDeletedAt());
    }
}
