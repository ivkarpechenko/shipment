<?php

namespace App\Tests\Domain\Country\Service;

use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Country\Service\RestoreCountryService;
use App\Tests\Fixture\Country\CountryFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RestoreCountryServiceTest extends KernelTestCase
{
    public function testRestoreCountry()
    {
        $oldCountry = CountryFixture::getOneForDeleted('test country', 'RU', deleted: true);
        $repositoryMock = $this->createMock(CountryRepositoryInterface::class);
        $repositoryMock->method('ofIdDeleted')->willReturn($oldCountry);

        $this->assertNotNull($oldCountry->getDeletedAt());

        $service = new RestoreCountryService($repositoryMock);

        $service->restore($oldCountry->getId());

        $this->assertNull($oldCountry->getDeletedAt());

        $repositoryMock->method('ofId')->willReturn($oldCountry);

        $country = $repositoryMock->ofId($oldCountry->getId());

        $this->assertNotNull($country);
        $this->assertNull($country->getDeletedAt());
    }
}
