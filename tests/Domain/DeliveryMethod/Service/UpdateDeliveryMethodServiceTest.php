<?php

declare(strict_types=1);

namespace App\Tests\Domain\DeliveryMethod\Service;

use App\Domain\DeliveryMethod\Entity\DeliveryMethod;
use App\Domain\DeliveryMethod\Exception\DeliveryMethodNotFoundException;
use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;
use App\Domain\DeliveryMethod\Service\UpdateDeliveryMethodService;
use App\Tests\Fixture\DeliveryMethod\DeliveryMethodFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class UpdateDeliveryMethodServiceTest extends KernelTestCase
{
    private DeliveryMethodRepositoryInterface $repositoryMock;

    public function setUp(): void
    {
        parent::setUp();
        $this->repositoryMock = $this->createMock(DeliveryMethodRepositoryInterface::class);
    }

    public function testUpdateDeliveryMethod(): void
    {
        $service = new UpdateDeliveryMethodService($this->repositoryMock);
        $this->repositoryMock->method('ofCode')->willReturn(DeliveryMethodFixture::getOne(
            'test',
            'test'
        ));

        $service->update('test', 'update test', false);

        $deliveryMethod = $this->repositoryMock->ofCode('test');

        $this->assertNotNull($deliveryMethod);
        $this->assertInstanceOf(DeliveryMethod::class, $deliveryMethod);
        $this->assertEquals('test', $deliveryMethod->getCode());
        $this->assertEquals('update test', $deliveryMethod->getName());
        $this->assertFalse($deliveryMethod->isActive());
        $this->assertInstanceOf(Uuid::class, $deliveryMethod->getId());
        $this->assertNotNull($deliveryMethod->getCreatedAt());
        $this->assertNotNull($deliveryMethod->getUpdatedAt());
    }

    public function testUpdateDeliveryMethodIfNotExists(): void
    {
        $service = new UpdateDeliveryMethodService($this->repositoryMock);

        $this->repositoryMock->method('ofCode')->willReturn(null);

        $this->expectException(DeliveryMethodNotFoundException::class);
        $service->update('test', 'test', true);
    }
}
