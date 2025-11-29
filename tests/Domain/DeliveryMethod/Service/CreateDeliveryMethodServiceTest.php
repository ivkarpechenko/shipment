<?php

declare(strict_types=1);

namespace App\Tests\Domain\DeliveryMethod\Service;

use App\Domain\DeliveryMethod\Entity\DeliveryMethod;
use App\Domain\DeliveryMethod\Exception\DeliveryMethodAlreadyCreatedException;
use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;
use App\Domain\DeliveryMethod\Service\CreateDeliveryMethodService;
use App\Tests\Fixture\DeliveryMethod\DeliveryMethodFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class CreateDeliveryMethodServiceTest extends KernelTestCase
{
    private DeliveryMethodRepositoryInterface $repositoryMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repositoryMock = $this->createMock(DeliveryMethodRepositoryInterface::class);
    }

    public function testCreateDeliveryMethod(): void
    {
        $service = new CreateDeliveryMethodService($this->repositoryMock);
        $service->create('test', 'test');

        $this->repositoryMock->method('ofCode')->willReturn(DeliveryMethodFixture::getOne(
            'test',
            'test'
        ));

        $deliveryMethod = $this->repositoryMock->ofCode('test');

        $this->assertNotNull($deliveryMethod);
        $this->assertInstanceOf(DeliveryMethod::class, $deliveryMethod);
        $this->assertEquals('test', $deliveryMethod->getCode());
        $this->assertEquals('test', $deliveryMethod->getName());
        $this->assertTrue($deliveryMethod->isActive());
        $this->assertInstanceOf(Uuid::class, $deliveryMethod->getId());
        $this->assertNotNull($deliveryMethod->getCreatedAt());
        $this->assertNull($deliveryMethod->getUpdatedAt());
    }

    public function testCreateDeliveryMethodIfExists(): void
    {
        $service = new CreateDeliveryMethodService($this->repositoryMock);

        $this->repositoryMock->method('ofCode')->willReturn(DeliveryMethodFixture::getOne(
            'test',
            'test'
        ));

        $this->expectException(DeliveryMethodAlreadyCreatedException::class);
        $service->create('test', 'test');
    }
}
