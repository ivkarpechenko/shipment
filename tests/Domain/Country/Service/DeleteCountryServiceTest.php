<?php

namespace App\Tests\Domain\Country\Service;

use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Country\Service\DeleteCountryService;
use App\Tests\Fixture\Country\CountryFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DeleteCountryServiceTest extends KernelTestCase
{
    public function testSoftDeleteCountry()
    {
        $oldCountry = CountryFixture::getOneForDeleted('test country', 'RU', deleted: false);
        $repositoryMock = $this->createMock(CountryRepositoryInterface::class);
        $repositoryMock->method('ofId')->willReturn($oldCountry);

        $service = new DeleteCountryService($repositoryMock);

        $this->assertNull($oldCountry->getDeletedAt());

        $service->delete($oldCountry->getId());

        $repositoryMock->method('ofId')->willReturn($oldCountry);

        $country = $repositoryMock->ofId($oldCountry->getId());

        $this->assertNotNull($country);
        $this->assertNotNull($country->getDeletedAt());
    }
}
