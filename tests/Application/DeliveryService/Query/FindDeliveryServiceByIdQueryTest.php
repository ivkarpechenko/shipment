<?php

namespace App\Tests\Application\DeliveryService\Query;

use App\Application\DeliveryService\Query\FindDeliveryServiceByIdQuery;
use App\Application\DeliveryService\Query\FindDeliveryServiceByIdQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class FindDeliveryServiceByIdQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindDeliveryServiceByIdQuery(Uuid::v1())
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindDeliveryServiceByIdQueryHandler::class)
        );
    }

    public function testFindDeliveryServiceByIdQueryHandler()
    {
        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $container = $this->getContainer();

        $repository = $container->get(DeliveryServiceRepositoryInterface::class);
        $repository->create($newDeliveryService);

        $deliveryService = $container->get(FindDeliveryServiceByIdQueryHandler::class)(
            new FindDeliveryServiceByIdQuery($newDeliveryService->getId())
        );

        $this->assertNotNull($deliveryService);
        $this->assertInstanceOf(DeliveryService::class, $deliveryService);
        $this->assertEquals('test', $deliveryService->getCode());
        $this->assertEquals('test', $deliveryService->getName());
        $this->assertTrue($deliveryService->isActive());
        $this->assertInstanceOf(Uuid::class, $deliveryService->getId());
        $this->assertEquals($newDeliveryService->getId(), $deliveryService->getId());
        $this->assertNotNull($deliveryService->getCreatedAt());
        $this->assertNull($deliveryService->getUpdatedAt());
    }
}
