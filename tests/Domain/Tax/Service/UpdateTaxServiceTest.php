<?php

namespace App\Tests\Domain\Tax\Service;

use App\Domain\Country\Entity\Country;
use App\Domain\Tax\Entity\Tax;
use App\Domain\Tax\Exception\TaxNotFoundException;
use App\Domain\Tax\Repository\TaxRepositoryInterface;
use App\Domain\Tax\Service\UpdateTaxService;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Tax\TaxFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class UpdateTaxServiceTest extends KernelTestCase
{
    private TaxRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->createMock(TaxRepositoryInterface::class);
    }

    public function testUpdateTaxName()
    {
        $country = CountryFixture::getOne('Russia', 'RU');

        $oldTax = TaxFixture::getOne($country, 'НДС', 0.2, 'price/(1+value)*value');
        $this->repository->method('ofId')->willReturn($oldTax);

        $service = new UpdateTaxService($this->repository);

        $service->update($oldTax->getId(), 0.18);

        $this->repository->method('ofId')->willReturn(TaxFixture::getOne(
            $country,
            'НДС',
            0.18,
            'price/(1+value)*value',
            $oldTax->getId()
        ));

        $newTax = $this->repository->ofId($oldTax->getId());

        $this->assertNotNull($newTax);
        $this->assertInstanceOf(Tax::class, $newTax);
        $this->assertNotNull($newTax->getCountry());
        $this->assertInstanceOf(Country::class, $newTax->getCountry());
        $this->assertEquals('НДС', $newTax->getName());
        $this->assertEquals(0.18, $newTax->getValue());
        $this->assertEquals('price/(1+value)*value', $newTax->getExpression());
        $this->assertNotNull($newTax->getCreatedAt());
    }

    public function testUpdateTaxNameIfNotFound()
    {
        $this->repository->method('ofId')->willReturn(null);

        $service = new UpdateTaxService($this->repository);

        $this->expectException(TaxNotFoundException::class);
        $service->update(Uuid::v1(), 0.20);
    }
}
