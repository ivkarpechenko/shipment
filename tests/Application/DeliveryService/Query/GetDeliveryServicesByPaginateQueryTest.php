<?php

namespace App\Tests\Application\DeliveryService\Query;

use App\Application\DeliveryService\Query\GetDeliveryServicesByPaginateQuery;
use App\Application\DeliveryService\Query\GetDeliveryServicesByPaginateQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\MessageBusTestCase;

class GetDeliveryServicesByPaginateQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new GetDeliveryServicesByPaginateQuery(0, 1)
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(GetDeliveryServicesByPaginateQueryHandler::class)
        );
    }

    public function testGetDeliveryServicesByPaginateQueryHandler()
    {
        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $newDeliveryService2 = DeliveryServiceFixture::getOne('test2', 'test2');
        $container = $this->getContainer();

        $repository = $container->get(DeliveryServiceRepositoryInterface::class);
        $repository->create($newDeliveryService);
        $repository->create($newDeliveryService2);

        $deliveryServices = $container->get(GetDeliveryServicesByPaginateQueryHandler::class)(
            new GetDeliveryServicesByPaginateQuery(0, 2)
        );

        $this->assertNotEmpty($deliveryServices);
        $this->assertIsArray($deliveryServices);
        $this->assertArrayHasKey('data', $deliveryServices);
        $this->assertArrayHasKey('total', $deliveryServices);
        $this->assertArrayHasKey('pages', $deliveryServices);
        $this->assertCount(2, $deliveryServices['data']);
        $this->assertEquals(2, $deliveryServices['total']);
        $this->assertEquals(1, $deliveryServices['pages']);

        $deliveryService = reset($deliveryServices['data']);

        $this->assertInstanceOf(DeliveryService::class, $deliveryService);
        $this->assertEquals('test', $deliveryService->getCode());
        $this->assertEquals('test', $deliveryService->getName());
        $this->assertTrue($deliveryService->isActive());
        $this->assertNotNull($deliveryService->getCreatedAt());
        $this->assertNull($deliveryService->getUpdatedAt());
    }
}
