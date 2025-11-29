<?php

namespace App\Tests\Infrastructure\DBAL\Repository\Doctrine\DeliveryMethod;

use App\Domain\DeliveryMethod\Entity\DeliveryMethod;
use App\Infrastructure\DBAL\Repository\Doctrine\DeliveryMethod\DoctrineDeliveryMethodRepository;
use App\Tests\DoctrineTestCase;
use App\Tests\Fixture\DeliveryMethod\DeliveryMethodFixture;

class DoctrineDeliveryMethodRepositoryTest extends DoctrineTestCase
{
    private DoctrineDeliveryMethodRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->getContainer()->get(DoctrineDeliveryMethodRepository::class);
    }

    public function testCreate(): void
    {
        self::bootKernel();
        $this->assertEmpty($this->repository->all());

        $newDeliveryMethod = DeliveryMethodFixture::getOne('test', 'test');

        $this->repository->create($newDeliveryMethod);

        $deliveryMethod = $this->repository->ofId($newDeliveryMethod->getId());

        $this->assertNotNull($deliveryMethod);
        $this->assertInstanceOf(DeliveryMethod::class, $deliveryMethod);
        $this->assertEquals($deliveryMethod->getId(), $deliveryMethod->getId());
        $this->assertEquals($deliveryMethod->getCode(), $deliveryMethod->getCode());
        $this->assertEquals($deliveryMethod->getName(), $deliveryMethod->getName());
    }

    public function testUpdate(): void
    {
        $this->assertEmpty($this->repository->all());

        $newDeliveryMethod = DeliveryMethodFixture::getOne('test', 'test');

        $this->repository->create($newDeliveryMethod);

        $this->assertEquals('test', $newDeliveryMethod->getCode());

        $deliveryMethod = $this->repository->ofId($newDeliveryMethod->getId());

        $deliveryMethod->changeName('updated test');
        $this->repository->update($deliveryMethod);

        $updatedDeliveryMethod = $this->repository->ofId($deliveryMethod->getId());

        $this->assertNotNull($updatedDeliveryMethod->getUpdatedAt());
        $this->assertEquals('updated test', $updatedDeliveryMethod->getName());

        $this->assertTrue($updatedDeliveryMethod->isActive());

        $updatedDeliveryMethod->changeIsActive(false);
        $this->repository->update($updatedDeliveryMethod);

        $deactivatedDeliveryMethod = $this->repository->ofId($updatedDeliveryMethod->getId());

        $this->assertNotNull($deactivatedDeliveryMethod);
        $this->assertFalse($deactivatedDeliveryMethod->isActive());
    }

    public function testOfId(): void
    {
        $this->assertEmpty($this->repository->all());

        $newDeliveryMethod = DeliveryMethodFixture::getOne('test', 'test');
        $this->repository->create($newDeliveryMethod);

        $deliveryMethod = $this->repository->ofId($newDeliveryMethod->getId());

        $this->assertNotNull($deliveryMethod);
        $this->assertInstanceOf(DeliveryMethod::class, $deliveryMethod);
        $this->assertEquals($deliveryMethod->getId(), $deliveryMethod->getId());
        $this->assertEquals($deliveryMethod->getCode(), $deliveryMethod->getCode());
        $this->assertEquals($deliveryMethod->getName(), $deliveryMethod->getName());
        $this->assertTrue($deliveryMethod->isActive());
        $this->assertNotNull($deliveryMethod->getCreatedAt());
        $this->assertNull($deliveryMethod->getUpdatedAt());
    }

    public function testOfCode(): void
    {
        $this->assertEmpty($this->repository->all());

        $newDeliveryMethod = DeliveryMethodFixture::getOne('test', 'test');
        $this->repository->create($newDeliveryMethod);

        $deliveryMethod = $this->repository->ofCode($newDeliveryMethod->getCode());

        $this->assertNotNull($deliveryMethod);
        $this->assertInstanceOf(DeliveryMethod::class, $deliveryMethod);
        $this->assertEquals($deliveryMethod->getId(), $deliveryMethod->getId());
        $this->assertEquals($deliveryMethod->getCode(), $deliveryMethod->getCode());
        $this->assertEquals($deliveryMethod->getName(), $deliveryMethod->getName());
        $this->assertTrue($deliveryMethod->isActive());
        $this->assertNotNull($deliveryMethod->getCreatedAt());
        $this->assertNull($deliveryMethod->getUpdatedAt());
    }

    public function testOfCodeDeactivated(): void
    {
        $this->assertEmpty($this->repository->all());

        $newDeliveryMethod = DeliveryMethodFixture::getOne('test', 'test');
        $newDeliveryMethod->deactivate();
        $this->repository->create($newDeliveryMethod);

        $deliveryMethod = $this->repository->ofCodeDeactivated($newDeliveryMethod->getCode());

        $this->assertNotNull($deliveryMethod);
        $this->assertInstanceOf(DeliveryMethod::class, $deliveryMethod);
        $this->assertEquals($deliveryMethod->getId(), $deliveryMethod->getId());
        $this->assertEquals($deliveryMethod->getCode(), $deliveryMethod->getCode());
        $this->assertEquals($deliveryMethod->getName(), $deliveryMethod->getName());
        $this->assertFalse($deliveryMethod->isActive());
        $this->assertNotNull($deliveryMethod->getCreatedAt());
        $this->assertNotNull($deliveryMethod->getUpdatedAt());
    }

    public function testAll(): void
    {
        $this->assertEmpty($this->repository->all());

        $firstDeliveryMethod = DeliveryMethodFixture::getOne('test1', 'test1');
        $this->repository->create($firstDeliveryMethod);
        $secondDeliveryMethod = DeliveryMethodFixture::getOne('test2', 'test2');
        $this->repository->create($secondDeliveryMethod);
        $deliveryMethods = $this->repository->all();

        $this->assertCount(2, $deliveryMethods);

        $deliveryMethod = $this->repository->ofId($firstDeliveryMethod->getId());
        $deliveryMethod->changeIsActive(false);
        $this->repository->update($deliveryMethod);
        $activeDeliveryMethods = $this->repository->all(true);

        $this->assertCount(1, $activeDeliveryMethods);

        $deactivatedDeliveryMethods = $this->repository->all(false);

        $this->assertCount(1, $deactivatedDeliveryMethods);
    }
}
