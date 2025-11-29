<?php

namespace App\Tests\Domain\Region\Service;

use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Domain\Region\Service\DeleteRegionService;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DeleteRegionServiceTest extends KernelTestCase
{
    public function testSoftDeleteRegion()
    {
        $country = CountryFixture::getOne('Russia', 'RU');

        $oldRegion = RegionFixture::getOneForDeleted($country, 'Moskva', 'MOW', false);
        $repositoryMock = $this->createMock(RegionRepositoryInterface::class);
        $repositoryMock->method('ofId')->willReturn($oldRegion);

        $service = new DeleteRegionService($repositoryMock);

        $this->assertNull($oldRegion->getDeletedAt());

        $service->delete($oldRegion->getId());

        $repositoryMock->method('ofId')->willReturn($oldRegion);

        $region = $repositoryMock->ofId($oldRegion->getId());

        $this->assertNotNull($region);
        $this->assertNotNull($region->getDeletedAt());
    }
}
