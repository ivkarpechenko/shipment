<?php

namespace App\Tests\Infrastructure\DBAL\Repository\Doctrine\Region;

use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Region\Entity\Region;
use App\Infrastructure\DBAL\Repository\Doctrine\Region\DoctrineRegionRepository;
use App\Tests\DoctrineTestCase;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;

class DoctrineRegionRepositoryTest extends DoctrineTestCase
{
    private DoctrineRegionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->getContainer()->get(DoctrineRegionRepository::class);
    }

    public function testCreateRegion()
    {
        $this->assertEmpty($this->repository->all());

        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $newRegion = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $this->repository->create($newRegion);

        $region = $this->repository->ofId($newRegion->getId());

        $this->assertNotNull($region);
        $this->assertInstanceOf(Region::class, $region);
        $this->assertEquals($newRegion->getId(), $region->getId());
        $this->assertEquals($newRegion->getName(), $region->getName());
        $this->assertEquals($newRegion->getCode(), $region->getCode());
    }

    public function testUpdateRegion()
    {
        $this->assertEmpty($this->repository->all());

        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $newRegion = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $this->repository->create($newRegion);

        $this->assertEquals('Moskva', $newRegion->getName());

        $region = $this->repository->ofId($newRegion->getId());

        $region->changeName('Moskva2');
        $this->repository->update($region);

        $updatedRegion = $this->repository->ofId($newRegion->getId());

        $this->assertNotNull($updatedRegion->getUpdatedAt());
        $this->assertEquals('Moskva2', $updatedRegion->getName());

        $this->assertTrue($updatedRegion->isActive());

        $updatedRegion->changeIsActive(false);
        $this->repository->update($updatedRegion);

        $deactivatedRegion = $this->repository->ofIdDeactivated($updatedRegion->getId());

        $this->assertNotNull($deactivatedRegion);
        $this->assertFalse($deactivatedRegion->isActive());
    }

    public function testDeleteRegion()
    {
        $this->assertEmpty($this->repository->all());

        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $newRegion = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $this->repository->create($newRegion);

        $createdRegion = $this->repository->ofId($newRegion->getId());
        $this->assertNotNull($createdRegion);
        $this->assertInstanceOf(Region::class, $createdRegion);
        $this->assertNull($createdRegion->getDeletedAt());

        $createdRegion->deleted();
        $this->repository->delete($createdRegion);

        $deletedRegion = $this->repository->ofIdDeleted($createdRegion->getId());

        $this->assertNotNull($deletedRegion);
        $this->assertNotNull($deletedRegion->getDeletedAt());
    }

    public function testRestoreRegion()
    {
        $this->assertEmpty($this->repository->all());

        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $newRegion = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $newRegion->deleted();
        $this->repository->create($newRegion);

        $deletedRegion = $this->repository->ofIdDeleted($newRegion->getId());
        $this->assertNotNull($deletedRegion);
        $this->assertInstanceOf(Region::class, $deletedRegion);
        $this->assertNotNull($deletedRegion->getDeletedAt());

        $deletedRegion->restored();
        $this->repository->restore($deletedRegion);

        $restoredRegion = $this->repository->ofId($deletedRegion->getId());

        $this->assertNotNull($restoredRegion);
        $this->assertNull($restoredRegion->getDeletedAt());
        $this->assertNotNull($restoredRegion->getUpdatedAt());
    }

    public function testOfId()
    {
        $this->assertEmpty($this->repository->all());

        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $newRegion = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $this->repository->create($newRegion);

        $region = $this->repository->ofId($newRegion->getId());

        $this->assertNotNull($region);
        $this->assertInstanceOf(Region::class, $region);
        $this->assertEquals($newRegion->getId(), $region->getId());
        $this->assertEquals($newRegion->getName(), $region->getName());
        $this->assertEquals($newRegion->getCode(), $region->getCode());
        $this->assertTrue($region->isActive());
        $this->assertNotNull($region->getCreatedAt());
        $this->assertNull($region->getUpdatedAt());
        $this->assertNull($region->getDeletedAt());
    }

    public function testOfCode()
    {
        $this->assertEmpty($this->repository->all());

        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $newRegion = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $this->repository->create($newRegion);

        $region = $this->repository->ofCode($newRegion->getCode());

        $this->assertNotNull($region);
        $this->assertInstanceOf(Region::class, $region);
        $this->assertEquals($newRegion->getId(), $region->getId());
        $this->assertEquals($newRegion->getName(), $region->getName());
        $this->assertEquals($newRegion->getCode(), $region->getCode());
        $this->assertTrue($region->isActive());
        $this->assertNotNull($region->getCreatedAt());
        $this->assertNull($region->getUpdatedAt());
        $this->assertNull($region->getDeletedAt());
    }

    public function testOfName()
    {
        $this->assertEmpty($this->repository->all());

        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $newRegion = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $this->repository->create($newRegion);

        $region = $this->repository->ofName($newRegion->getName());

        $this->assertNotNull($region);
        $this->assertInstanceOf(Region::class, $region);
        $this->assertEquals($newRegion->getId(), $region->getId());
        $this->assertEquals($newRegion->getName(), $region->getName());
        $this->assertEquals($newRegion->getCode(), $region->getCode());
        $this->assertTrue($region->isActive());
        $this->assertNotNull($region->getCreatedAt());
        $this->assertNull($region->getUpdatedAt());
        $this->assertNull($region->getDeletedAt());
    }

    public function testOfIdDeleted()
    {
        $this->assertEmpty($this->repository->all());

        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $newRegion = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $newRegion->deleted();
        $this->repository->create($newRegion);

        $deletedRegion = $this->repository->ofIdDeleted($newRegion->getId());

        $this->assertNotNull($deletedRegion);
        $this->assertInstanceOf(Region::class, $deletedRegion);
        $this->assertEquals($newRegion->getId(), $deletedRegion->getId());
        $this->assertEquals($newRegion->getName(), $deletedRegion->getName());
        $this->assertEquals($newRegion->getCode(), $deletedRegion->getCode());
        $this->assertTrue($deletedRegion->isActive());
        $this->assertNotNull($deletedRegion->getCreatedAt());
        $this->assertNull($deletedRegion->getUpdatedAt());
        $this->assertNotNull($deletedRegion->getDeletedAt());
    }

    public function testOfIdDeactivated()
    {
        $this->assertEmpty($this->repository->all());

        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $newRegion = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $newRegion->changeIsActive(false);
        $this->repository->create($newRegion);

        $deactivatedRegion = $this->repository->ofIdDeactivated($newRegion->getId());

        $this->assertNotNull($deactivatedRegion);
        $this->assertInstanceOf(Region::class, $deactivatedRegion);
        $this->assertEquals($newRegion->getId(), $deactivatedRegion->getId());
        $this->assertEquals($newRegion->getName(), $deactivatedRegion->getName());
        $this->assertEquals($newRegion->getCode(), $deactivatedRegion->getCode());
        $this->assertFalse($deactivatedRegion->isActive());
        $this->assertNotNull($deactivatedRegion->getCreatedAt());
        $this->assertNotNull($deactivatedRegion->getUpdatedAt());
        $this->assertNull($deactivatedRegion->getDeletedAt());
    }

    public function testOfCodeDeleted()
    {
        $this->assertEmpty($this->repository->all());

        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $newRegion = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $newRegion->deleted();
        $this->repository->create($newRegion);

        $deletedRegion = $this->repository->ofCodeDeleted($newRegion->getCode());

        $this->assertNotNull($deletedRegion);
        $this->assertInstanceOf(Region::class, $deletedRegion);
        $this->assertEquals($newRegion->getId(), $deletedRegion->getId());
        $this->assertEquals($newRegion->getName(), $deletedRegion->getName());
        $this->assertEquals($newRegion->getCode(), $deletedRegion->getCode());
        $this->assertTrue($deletedRegion->isActive());
        $this->assertNotNull($deletedRegion->getCreatedAt());
        $this->assertNull($deletedRegion->getUpdatedAt());
        $this->assertNotNull($deletedRegion->getDeletedAt());
    }

    public function testOfCodeDeactivated()
    {
        $this->assertEmpty($this->repository->all());

        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $newRegion = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $newRegion->changeIsActive(false);
        $this->repository->create($newRegion);

        $deactivatedRegion = $this->repository->ofCodeDeactivated($newRegion->getCode());

        $this->assertNotNull($deactivatedRegion);
        $this->assertInstanceOf(Region::class, $deactivatedRegion);
        $this->assertEquals($newRegion->getId(), $deactivatedRegion->getId());
        $this->assertEquals($newRegion->getName(), $deactivatedRegion->getName());
        $this->assertEquals($newRegion->getCode(), $deactivatedRegion->getCode());
        $this->assertFalse($deactivatedRegion->isActive());
        $this->assertNotNull($deactivatedRegion->getCreatedAt());
        $this->assertNotNull($deactivatedRegion->getUpdatedAt());
        $this->assertNull($deactivatedRegion->getDeletedAt());
    }
}
