<?php

namespace App\Tests\Domain\Tax\Service;

use App\Domain\Tax\Repository\TaxRepositoryInterface;
use App\Domain\Tax\Service\DeleteTaxService;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Tax\TaxFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DeleteTaxServiceTest extends KernelTestCase
{
    public function testSoftDeleteTax()
    {
        $country = CountryFixture::getOne('Russia', 'RU');

        $oldTax = TaxFixture::getOne($country, 'НДС', 0.2, 'price/(1+value)*value');
        $repositoryMock = $this->createMock(TaxRepositoryInterface::class);
        $repositoryMock->method('ofId')->willReturn($oldTax);

        $service = new DeleteTaxService($repositoryMock);

        $this->assertNull($oldTax->getDeletedAt());

        $service->delete($oldTax->getId());

        $repositoryMock->method('ofId')->willReturn($oldTax);

        $tax = $repositoryMock->ofId($oldTax->getId());

        $this->assertNotNull($tax);
        $this->assertNotNull($tax->getDeletedAt());
    }
}
