<?php

namespace App\Tests\Application\DeliveryService\Query;

use App\Application\DeliveryService\Query\FindDeliveryServiceByCodeQuery;
use App\Application\DeliveryService\Query\FindDeliveryServiceByCodeQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class FindDeliveryServiceByCodeQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindDeliveryServiceByCodeQuery('test')
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindDeliveryServiceByCodeQueryHandler::class)
        );
    }

    public function testFindDeliveryServiceByCodeQueryHandler()
    {
        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $container = $this->getContainer();

        $repository = $container->get(DeliveryServiceRepositoryInterface::class);
        $repository->create($newDeliveryService);

        $deliveryService = $container->get(FindDeliveryServiceByCodeQueryHandler::class)(
            new FindDeliveryServiceByCodeQuery('test')
        );

        $this->assertNotNull($deliveryService);
        $this->assertInstanceOf(DeliveryService::class, $deliveryService);
        $this->assertEquals('test', $deliveryService->getCode());
        $this->assertEquals('test', $deliveryService->getName());
        $this->assertTrue($deliveryService->isActive());
        $this->assertInstanceOf(Uuid::class, $deliveryService->getId());
        $this->assertNotNull($deliveryService->getCreatedAt());
        $this->assertNull($deliveryService->getUpdatedAt());
    }
}
