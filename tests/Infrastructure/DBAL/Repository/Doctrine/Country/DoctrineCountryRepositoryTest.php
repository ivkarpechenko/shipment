<?php

namespace App\Tests\Infrastructure\DBAL\Repository\Doctrine\Country;

use App\Domain\Country\Entity\Country;
use App\Infrastructure\DBAL\Repository\Doctrine\Country\DoctrineCountryRepository;
use App\Tests\DoctrineTestCase;
use App\Tests\Fixture\Country\CountryFixture;

class DoctrineCountryRepositoryTest extends DoctrineTestCase
{
    private DoctrineCountryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->getContainer()->get(DoctrineCountryRepository::class);
    }

    public function testCreateCountry()
    {
        $this->assertEmpty($this->repository->all());

        $newCountry = CountryFixture::getOne('test country', 'RU');

        $this->repository->create($newCountry);

        $country = $this->repository->ofId($newCountry->getId());

        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals($newCountry->getId(), $country->getId());
        $this->assertEquals($newCountry->getName(), $country->getName());
        $this->assertEquals($newCountry->getCode(), $country->getCode());
    }

    public function testUpdateCountry()
    {
        $this->assertEmpty($this->repository->all());

        $newCountry = CountryFixture::getOne('test country', 'RU');
        $this->repository->create($newCountry);

        $this->assertEquals('test country', $newCountry->getName());

        $country = $this->repository->ofId($newCountry->getId());

        $country->changeName('updated test country');
        $this->repository->update($country);

        $updatedCountry = $this->repository->ofId($newCountry->getId());

        $this->assertNotNull($updatedCountry->getUpdatedAt());
        $this->assertEquals('updated test country', $updatedCountry->getName());

        $this->assertTrue($updatedCountry->isActive());

        $updatedCountry->changeIsActive(false);
        $this->repository->update($updatedCountry);

        $deactivatedCountry = $this->repository->ofIdDeactivated($updatedCountry->getId());

        $this->assertNotNull($deactivatedCountry);
        $this->assertFalse($deactivatedCountry->isActive());
    }

    public function testDeleteCountry()
    {
        $this->assertEmpty($this->repository->all());

        $newCountry = CountryFixture::getOne('test country', 'RU');
        $this->repository->create($newCountry);

        $createdCountry = $this->repository->ofId($newCountry->getId());
        $this->assertNotNull($createdCountry);
        $this->assertInstanceOf(Country::class, $createdCountry);
        $this->assertNull($createdCountry->getDeletedAt());

        $createdCountry->deleted();
        $this->repository->delete($createdCountry);

        $deletedCountry = $this->repository->ofIdDeleted($createdCountry->getId());

        $this->assertNotNull($deletedCountry);
        $this->assertNotNull($deletedCountry->getDeletedAt());
    }

    public function testRestoreCountry()
    {
        $this->assertEmpty($this->repository->all());

        $newCountry = CountryFixture::getOne('test country', 'RU');
        $newCountry->deleted();
        $this->repository->create($newCountry);

        $deletedCountry = $this->repository->ofIdDeleted($newCountry->getId());
        $this->assertNotNull($deletedCountry);
        $this->assertInstanceOf(Country::class, $deletedCountry);
        $this->assertNotNull($deletedCountry->getDeletedAt());

        $deletedCountry->restored();
        $this->repository->restore($deletedCountry);

        $restoredCountry = $this->repository->ofId($deletedCountry->getId());

        $this->assertNotNull($restoredCountry);
        $this->assertNull($restoredCountry->getDeletedAt());
        $this->assertNotNull($restoredCountry->getUpdatedAt());
    }

    public function testOfId()
    {
        $this->assertEmpty($this->repository->all());

        $newCountry = CountryFixture::getOne('test country', 'RU');
        $this->repository->create($newCountry);

        $country = $this->repository->ofId($newCountry->getId());

        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals($newCountry->getId(), $country->getId());
        $this->assertEquals($newCountry->getName(), $country->getName());
        $this->assertEquals($newCountry->getCode(), $country->getCode());
        $this->assertTrue($country->isActive());
        $this->assertNotNull($country->getCreatedAt());
        $this->assertNull($country->getUpdatedAt());
        $this->assertNull($country->getDeletedAt());
    }

    public function testOfCode()
    {
        $this->assertEmpty($this->repository->all());

        $newCountry = CountryFixture::getOne('test country', 'RU');
        $this->repository->create($newCountry);

        $country = $this->repository->ofCode($newCountry->getCode());

        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals($newCountry->getId(), $country->getId());
        $this->assertEquals($newCountry->getName(), $country->getName());
        $this->assertEquals($newCountry->getCode(), $country->getCode());
        $this->assertTrue($country->isActive());
        $this->assertNotNull($country->getCreatedAt());
        $this->assertNull($country->getUpdatedAt());
        $this->assertNull($country->getDeletedAt());
    }

    public function testOfName()
    {
        $this->assertEmpty($this->repository->all());

        $newCountry = CountryFixture::getOne('test country', 'RU');
        $this->repository->create($newCountry);

        $country = $this->repository->ofName($newCountry->getName());

        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals($newCountry->getId(), $country->getId());
        $this->assertEquals($newCountry->getName(), $country->getName());
        $this->assertEquals($newCountry->getCode(), $country->getCode());
        $this->assertTrue($country->isActive());
        $this->assertNotNull($country->getCreatedAt());
        $this->assertNull($country->getUpdatedAt());
        $this->assertNull($country->getDeletedAt());
    }

    public function testOfIdDeleted()
    {
        $this->assertEmpty($this->repository->all());

        $newCountry = CountryFixture::getOne('test country', 'RU');
        $newCountry->deleted();
        $this->repository->create($newCountry);

        $deletedCountry = $this->repository->ofIdDeleted($newCountry->getId());

        $this->assertNotNull($deletedCountry);
        $this->assertInstanceOf(Country::class, $deletedCountry);
        $this->assertEquals($newCountry->getId(), $deletedCountry->getId());
        $this->assertEquals($newCountry->getName(), $deletedCountry->getName());
        $this->assertEquals($newCountry->getCode(), $deletedCountry->getCode());
        $this->assertTrue($deletedCountry->isActive());
        $this->assertNotNull($deletedCountry->getCreatedAt());
        $this->assertNull($deletedCountry->getUpdatedAt());
        $this->assertNotNull($deletedCountry->getDeletedAt());
    }

    public function testOfIdDeactivated()
    {
        $this->assertEmpty($this->repository->all());

        $newCountry = CountryFixture::getOne('test country', 'RU');
        $newCountry->changeIsActive(false);
        $this->repository->create($newCountry);

        $deactivatedCountry = $this->repository->ofIdDeactivated($newCountry->getId());

        $this->assertNotNull($deactivatedCountry);
        $this->assertInstanceOf(Country::class, $deactivatedCountry);
        $this->assertEquals($newCountry->getId(), $deactivatedCountry->getId());
        $this->assertEquals($newCountry->getName(), $deactivatedCountry->getName());
        $this->assertEquals($newCountry->getCode(), $deactivatedCountry->getCode());
        $this->assertFalse($deactivatedCountry->isActive());
        $this->assertNotNull($deactivatedCountry->getCreatedAt());
        $this->assertNotNull($deactivatedCountry->getUpdatedAt());
        $this->assertNull($deactivatedCountry->getDeletedAt());
    }

    public function testOfCodeDeleted()
    {
        $this->assertEmpty($this->repository->all());

        $newCountry = CountryFixture::getOne('test country', 'RU');
        $newCountry->deleted();
        $this->repository->create($newCountry);

        $deletedCountry = $this->repository->ofCodeDeleted($newCountry->getCode());

        $this->assertNotNull($deletedCountry);
        $this->assertInstanceOf(Country::class, $deletedCountry);
        $this->assertEquals($newCountry->getId(), $deletedCountry->getId());
        $this->assertEquals($newCountry->getName(), $deletedCountry->getName());
        $this->assertEquals($newCountry->getCode(), $deletedCountry->getCode());
        $this->assertTrue($deletedCountry->isActive());
        $this->assertNotNull($deletedCountry->getCreatedAt());
        $this->assertNull($deletedCountry->getUpdatedAt());
        $this->assertNotNull($deletedCountry->getDeletedAt());
    }

    public function testOfCodeDeactivated()
    {
        $this->assertEmpty($this->repository->all());

        $newCountry = CountryFixture::getOne('test country', 'RU');
        $newCountry->changeIsActive(false);
        $this->repository->create($newCountry);

        $deactivatedCountry = $this->repository->ofCodeDeactivated($newCountry->getCode());

        $this->assertNotNull($deactivatedCountry);
        $this->assertInstanceOf(Country::class, $deactivatedCountry);
        $this->assertEquals($newCountry->getId(), $deactivatedCountry->getId());
        $this->assertEquals($newCountry->getName(), $deactivatedCountry->getName());
        $this->assertEquals($newCountry->getCode(), $deactivatedCountry->getCode());
        $this->assertFalse($deactivatedCountry->isActive());
        $this->assertNotNull($deactivatedCountry->getCreatedAt());
        $this->assertNotNull($deactivatedCountry->getUpdatedAt());
        $this->assertNull($deactivatedCountry->getDeletedAt());
    }
}
