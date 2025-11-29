<?php

namespace App\Tests\Domain\Tax\Service;

use App\Domain\Tax\Repository\TaxRepositoryInterface;
use App\Domain\Tax\Service\RestoreTaxService;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Tax\TaxFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RestoreTaxServiceTest extends KernelTestCase
{
    public function testRestoreTax()
    {
        $country = CountryFixture::getOne('Russia', 'RU');
        $oldTax = TaxFixture::getOneForDeleted($country, 'НДС', 0.2, 'price/(1+value)*value');
        $repositoryMock = $this->createMock(TaxRepositoryInterface::class);
        $repositoryMock->method('ofIdDeleted')->willReturn($oldTax);

        $this->assertNotNull($oldTax->getDeletedAt());

        $service = new RestoreTaxService($repositoryMock);

        $service->restore($oldTax->getId());

        $this->assertNull($oldTax->getDeletedAt());

        $repositoryMock->method('ofId')->willReturn($oldTax);

        $tax = $repositoryMock->ofId($oldTax->getId());

        $this->assertNotNull($tax);
        $this->assertNull($tax->getDeletedAt());
    }
}
