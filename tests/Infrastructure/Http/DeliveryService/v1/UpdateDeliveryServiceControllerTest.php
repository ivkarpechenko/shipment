<?php

namespace App\Tests\Infrastructure\Http\DeliveryService\v1;

use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\HttpTestCase;

class UpdateDeliveryServiceControllerTest extends HttpTestCase
{
    public function testUpdateDeliveryServiceName()
    {
        $repository = $this->getContainer()->get(DeliveryServiceRepositoryInterface::class);
        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test service');

        $repository->create($newDeliveryService);

        $this->client->request(
            'PUT',
            "/api/v1/delivery-service/{$newDeliveryService->getCode()}",
            [
                'name' => 'Updated test service',
            ]
        );

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(200);

        $deliveryService = $repository->ofCode($newDeliveryService->getCode());

        $this->assertNotNull($deliveryService);
        $this->assertInstanceOf(DeliveryService::class, $deliveryService);
        $this->assertEquals('test', $deliveryService->getCode());
        $this->assertEquals('Updated test service', $deliveryService->getName());
        $this->assertTrue($deliveryService->isActive());
        $this->assertNotNull($deliveryService->getCreatedAt());
        $this->assertNotNull($deliveryService->getUpdatedAt());
    }

    public function testUpdateDeliveryServiceIsActive()
    {
        $repository = $this->getContainer()->get(DeliveryServiceRepositoryInterface::class);
        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test service');

        $repository->create($newDeliveryService);

        $this->client->request(
            'PUT',
            "/api/v1/delivery-service/{$newDeliveryService->getCode()}",
            [
                'isActive' => false,
            ]
        );

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(200);

        $deliveryService = $repository->ofCodeDeactivated($newDeliveryService->getCode());

        $this->assertNotNull($deliveryService);
        $this->assertInstanceOf(DeliveryService::class, $deliveryService);
        $this->assertEquals('test', $deliveryService->getCode());
        $this->assertEquals('test service', $deliveryService->getName());
        $this->assertFalse($deliveryService->isActive());
        $this->assertNotNull($deliveryService->getCreatedAt());
        $this->assertNotNull($deliveryService->getUpdatedAt());
    }
}
