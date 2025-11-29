<?php

namespace App\Tests\Application\DeliveryService\Query;

use App\Application\DeliveryService\Query\GetAllDeliveryServicesQuery;
use App\Application\DeliveryService\Query\GetAllDeliveryServicesQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\MessageBusTestCase;

class GetAllDeliveryServicesQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new GetAllDeliveryServicesQuery()
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(GetAllDeliveryServicesQueryHandler::class)
        );
    }

    public function testGetAllDeliveryServicesQueryHandler()
    {
        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $container = $this->getContainer();

        $repository = $container->get(DeliveryServiceRepositoryInterface::class);
        $repository->create($newDeliveryService);

        $deliveryServices = $container->get(GetAllDeliveryServicesQueryHandler::class)(
            new GetAllDeliveryServicesQuery()
        );

        $this->assertNotEmpty($deliveryServices);
        $this->assertIsArray($deliveryServices);

        $deliveryService = reset($deliveryServices);

        $this->assertInstanceOf(DeliveryService::class, $deliveryService);
        $this->assertEquals('test', $deliveryService->getCode());
        $this->assertEquals('test', $deliveryService->getCode());
        $this->assertTrue($deliveryService->isActive());
        $this->assertNotNull($deliveryService->getCreatedAt());
        $this->assertNull($deliveryService->getUpdatedAt());
    }
}
