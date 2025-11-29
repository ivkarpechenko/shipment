<?php

namespace App\Tests\Infrastructure\DBAL\Repository\Doctrine\Tax;

use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Tax\Entity\Tax;
use App\Infrastructure\DBAL\Repository\Doctrine\Tax\DoctrineTaxRepository;
use App\Tests\DoctrineTestCase;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Tax\TaxFixture;

class DoctrineTaxRepositoryTest extends DoctrineTestCase
{
    private DoctrineTaxRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->getContainer()->get(DoctrineTaxRepository::class);
    }

    public function testCreateTax()
    {
        $this->assertEmpty($this->repository->all());

        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $newTax = TaxFixture::getOne($country, 'НДС', 0.2, 'price/(1+value)*value');
        $this->repository->create($newTax);

        $tax = $this->repository->ofId($newTax->getId());

        $this->assertNotNull($tax);
        $this->assertInstanceOf(Tax::class, $tax);
        $this->assertEquals($newTax->getId(), $tax->getId());
        $this->assertEquals($newTax->getName(), $tax->getName());
        $this->assertEquals($newTax->getValue(), $tax->getValue());
        $this->assertEquals($newTax->getExpression(), $tax->getExpression());
    }

    public function testUpdateTax()
    {
        $this->assertEmpty($this->repository->all());

        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $newTax = TaxFixture::getOne($country, 'НДС', 0.2, 'price/(1+value)*value');
        $this->repository->create($newTax);

        $this->assertEquals(0.2, $newTax->getValue());

        $tax = $this->repository->ofId($newTax->getId());

        $tax->changeValue(0.18);
        $this->repository->update($tax);

        $updatedTax = $this->repository->ofId($newTax->getId());

        $this->assertNotNull($updatedTax->getUpdatedAt());
        $this->assertEquals(0.18, $updatedTax->getValue());
    }

    public function testDeleteTax()
    {
        $this->assertEmpty($this->repository->all());

        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $newTax = TaxFixture::getOne($country, 'НДС', 0.2, 'price/(1+value)*value');
        $this->repository->create($newTax);

        $createdTax = $this->repository->ofId($newTax->getId());
        $this->assertNotNull($createdTax);
        $this->assertInstanceOf(Tax::class, $createdTax);
        $this->assertNull($createdTax->getDeletedAt());

        $createdTax->deleted();
        $this->repository->delete($createdTax);

        $deletedTax = $this->repository->ofIdDeleted($createdTax->getId());

        $this->assertNotNull($deletedTax);
        $this->assertNotNull($deletedTax->getDeletedAt());
    }

    public function testRestoreTax()
    {
        $this->assertEmpty($this->repository->all());

        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $newTax = TaxFixture::getOne($country, 'НДС', 0.2, 'price/(1+value)*value');
        $newTax->deleted();
        $this->repository->create($newTax);

        $deletedTax = $this->repository->ofIdDeleted($newTax->getId());
        $this->assertNotNull($deletedTax);
        $this->assertInstanceOf(Tax::class, $deletedTax);
        $this->assertNotNull($deletedTax->getDeletedAt());

        $deletedTax->restored();
        $this->repository->restore($deletedTax);

        $restoredTax = $this->repository->ofId($deletedTax->getId());

        $this->assertNotNull($restoredTax);
        $this->assertNull($restoredTax->getDeletedAt());
        $this->assertNotNull($restoredTax->getUpdatedAt());
    }

    public function testOfId()
    {
        $this->assertEmpty($this->repository->all());

        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $newTax = TaxFixture::getOne($country, 'НДС', 0.2, 'price/(1+value)*value');
        $this->repository->create($newTax);

        $tax = $this->repository->ofId($newTax->getId());

        $this->assertNotNull($tax);
        $this->assertInstanceOf(Tax::class, $tax);
        $this->assertEquals($newTax->getId(), $tax->getId());
        $this->assertEquals($newTax->getName(), $tax->getName());
        $this->assertEquals($newTax->getValue(), $tax->getValue());
        $this->assertEquals($newTax->getExpression(), $tax->getExpression());
        $this->assertNotNull($tax->getCreatedAt());
        $this->assertNull($tax->getUpdatedAt());
        $this->assertNull($tax->getDeletedAt());
    }

    public function testOfIdDeleted()
    {
        $this->assertEmpty($this->repository->all());

        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $newTax = TaxFixture::getOne($country, 'НДС', 0.2, 'price/(1+value)*value');
        $newTax->deleted();
        $this->repository->create($newTax);

        $deletedTax = $this->repository->ofIdDeleted($newTax->getId());

        $this->assertNotNull($deletedTax);
        $this->assertInstanceOf(Tax::class, $deletedTax);
        $this->assertEquals($newTax->getId(), $deletedTax->getId());
        $this->assertEquals($newTax->getName(), $deletedTax->getName());
        $this->assertEquals($newTax->getValue(), $deletedTax->getValue());
        $this->assertEquals($newTax->getExpression(), $deletedTax->getExpression());
        $this->assertNotNull($deletedTax->getCreatedAt());
        $this->assertNull($deletedTax->getUpdatedAt());
        $this->assertNotNull($deletedTax->getDeletedAt());
    }

    public function testOfCountryAndName()
    {
        $this->assertEmpty($this->repository->all());

        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $newTax = TaxFixture::getOne($country, 'НДС', 0.2, 'price/(1+value)*value');
        $this->repository->create($newTax);

        $tax = $this->repository->ofCountryAndName($country, $newTax->getName());

        $this->assertNotNull($tax);
        $this->assertInstanceOf(Tax::class, $tax);
        $this->assertEquals($newTax->getId(), $tax->getId());
        $this->assertEquals($newTax->getName(), $tax->getName());
        $this->assertEquals($newTax->getValue(), $tax->getValue());
        $this->assertEquals($newTax->getExpression(), $tax->getExpression());
        $this->assertNotNull($tax->getCreatedAt());
        $this->assertNull($tax->getUpdatedAt());
        $this->assertNull($tax->getDeletedAt());
    }

    public function testOfCountryAndNameDeleted()
    {
        $this->assertEmpty($this->repository->all());

        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $newTax = TaxFixture::getOneForDeleted($country, 'НДС', 0.2, 'price/(1+value)*value');

        $this->repository->create($newTax);

        $tax = $this->repository->ofCountryAndNameDeleted($country, $newTax->getName());

        $this->assertNotNull($tax);
        $this->assertInstanceOf(Tax::class, $tax);
        $this->assertEquals($newTax->getId(), $tax->getId());
        $this->assertEquals($newTax->getName(), $tax->getName());
        $this->assertEquals($newTax->getValue(), $tax->getValue());
        $this->assertEquals($newTax->getExpression(), $tax->getExpression());
        $this->assertNotNull($tax->getCreatedAt());
        $this->assertNull($tax->getUpdatedAt());
        $this->assertNotNull($tax->getDeletedAt());
    }
}
