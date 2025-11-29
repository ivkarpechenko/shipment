<?php

declare(strict_types=1);

namespace App\Tests\Application\DeliveryService\Query;

use App\Application\DeliveryService\Query\FindDeliveryServiceRestrictPackageByDeliveryServiceIdQuery;
use App\Application\DeliveryService\Query\FindDeliveryServiceRestrictPackageByDeliveryServiceIdQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Entity\DeliveryServiceRestrictPackage;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\DeliveryService\Repository\DeliveryServiceRestrictPackageRepositoryInterface;
use App\Tests\Fixture\DeliveryService\DeliverServiceRestrictPackageFixture;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class FindDeliveryServiceRestrictPackageByDeliveryServiceIdQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf(): void
    {
        $this->assertInstanceOf(
            Query::class,
            new FindDeliveryServiceRestrictPackageByDeliveryServiceIdQuery(Uuid::v1())
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindDeliveryServiceRestrictPackageByDeliveryServiceIdQueryHandler::class)
        );
    }

    public function testFindDeliveryServiceRestrictPackageByDeliveryServiceIdQueryHandler(): void
    {
        $container = $this->getContainer();

        $deliveryServiceRepository = $container->get(DeliveryServiceRepositoryInterface::class);
        $deliveryServiceRestrictPackageRepository = $container->get(DeliveryServiceRestrictPackageRepositoryInterface::class);

        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $deliveryServiceRepository->create($newDeliveryService);
        $deliveryService = $deliveryServiceRepository->ofId($newDeliveryService->getId());

        $newDeliveryServiceRestrictPackage = DeliverServiceRestrictPackageFixture::getOne(
            $deliveryService,
            10,
            20,
            30,
            40
        );

        $deliveryServiceRestrictPackageRepository->create($newDeliveryServiceRestrictPackage);

        $deliveryServiceRestrictPackage = $container->get(FindDeliveryServiceRestrictPackageByDeliveryServiceIdQueryHandler::class)(
            new FindDeliveryServiceRestrictPackageByDeliveryServiceIdQuery($deliveryService->getId())
        );

        $this->assertNotNull($deliveryServiceRestrictPackage);
        $this->assertInstanceOf(DeliveryServiceRestrictPackage::class, $deliveryServiceRestrictPackage);
        $this->assertInstanceOf(DeliveryService::class, $deliveryServiceRestrictPackage->getDeliveryService());
        $this->assertEquals(10, $deliveryServiceRestrictPackage->getMaxWeight());
        $this->assertEquals(20, $deliveryServiceRestrictPackage->getMaxWidth());
        $this->assertEquals(30, $deliveryServiceRestrictPackage->getMaxHeight());
        $this->assertEquals(40, $deliveryServiceRestrictPackage->getMaxLength());
        $this->assertTrue($deliveryServiceRestrictPackage->isActive());
        $this->assertInstanceOf(Uuid::class, $deliveryServiceRestrictPackage->getId());
        $this->assertNotNull($deliveryServiceRestrictPackage->getCreatedAt());
        $this->assertNull($deliveryServiceRestrictPackage->getUpdatedAt());
    }
}
