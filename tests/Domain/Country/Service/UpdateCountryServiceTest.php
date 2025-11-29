<?php

namespace App\Tests\Domain\Country\Service;

use App\Domain\Country\Entity\Country;
use App\Domain\Country\Exception\CountryNotFoundException;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Country\Service\UpdateCountryService;
use App\Tests\Fixture\Country\CountryFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class UpdateCountryServiceTest extends KernelTestCase
{
    private CountryRepositoryInterface $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->createMock(CountryRepositoryInterface::class);
    }

    public function testUpdateCountryName()
    {
        $oldCountry = CountryFixture::getOne('test old country', 'RU');
        $this->repository->method('ofId')->willReturn($oldCountry);

        $service = new UpdateCountryService($this->repository);

        $service->update($oldCountry->getId(), 'test new country', null);

        $this->repository->method('ofId')->willReturn(CountryFixture::getOne(
            'test new country',
            'RU',
            $oldCountry->getId()
        ));

        $newCountry = $this->repository->ofId($oldCountry->getId());

        $this->assertNotNull($newCountry);
        $this->assertInstanceOf(Country::class, $newCountry);
        $this->assertEquals('test new country', $newCountry->getName());
        $this->assertEquals('RU', $newCountry->getCode());
        $this->assertNotNull($newCountry->getCreatedAt());
        $this->assertNotNull($newCountry->getUpdatedAt());
    }

    public function testUpdateCountryNameIfNotFound()
    {
        $this->repository->method('ofId')->willReturn(null);

        $service = new UpdateCountryService($this->repository);

        $this->expectException(CountryNotFoundException::class);
        $service->update(Uuid::v1(), 'test new country', null);
    }

    public function testUpdateCountryIsActive()
    {
        $oldCountry = CountryFixture::getOneForIsActive('test country', 'RU', true);
        $this->repository->method('ofId')->willReturn($oldCountry);

        $service = new UpdateCountryService($this->repository);

        $this->assertTrue($oldCountry->isActive());

        $service->update($oldCountry->getId(), null, false);

        $this->repository->method('ofId')->willReturn(CountryFixture::getOneForIsActive(
            'test country',
            'RU',
            false,
            $oldCountry->getId()
        ));

        $newCountry = $this->repository->ofId($oldCountry->getId());

        $this->assertNotNull($newCountry);
        $this->assertInstanceOf(Country::class, $newCountry);
        $this->assertEquals('test country', $newCountry->getName());
        $this->assertEquals('RU', $newCountry->getCode());
        $this->assertFalse($newCountry->isActive());
        $this->assertNotNull($newCountry->getCreatedAt());
        $this->assertNotNull($newCountry->getUpdatedAt());
    }

    public function testUpdateCountryIsActiveIfNotFound()
    {
        $service = new UpdateCountryService($this->repository);

        $this->expectException(CountryNotFoundException::class);
        $service->update(Uuid::v1(), null, true);
    }
}
