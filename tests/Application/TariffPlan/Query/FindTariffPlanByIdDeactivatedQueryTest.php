<?php

namespace App\Tests\Application\TariffPlan\Query;

use App\Application\Query;
use App\Application\QueryHandler;
use App\Application\TariffPlan\Query\FindTariffPlanByIdDeactivatedQuery;
use App\Application\TariffPlan\Query\FindTariffPlanByIdDeactivatedQueryHandler;
use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\TariffPlan\Entity\TariffPlan;
use App\Domain\TariffPlan\Repository\TariffPlanRepositoryInterface;
use App\Tests\Fixture\DeliveryMethod\DeliveryMethodFixture;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\Fixture\TariffPlan\TariffPlanFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class FindTariffPlanByIdDeactivatedQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindTariffPlanByIdDeactivatedQuery(Uuid::v1())
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindTariffPlanByIdDeactivatedQueryHandler::class)
        );
    }

    public function testFindTariffPlanByIdDeactivatedQueryHandler()
    {
        $container = $this->getContainer();

        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $deliveryServiceRepository = $container->get(DeliveryServiceRepositoryInterface::class);
        $deliveryServiceRepository->create($newDeliveryService);

        $newDeliveryMethod = DeliveryMethodFixture::getOne('test', 'test');
        $deliveryMethodRepository = $container->get(DeliveryMethodRepositoryInterface::class);
        $deliveryMethodRepository->create($newDeliveryMethod);

        $deliveryService = $deliveryServiceRepository->ofId($newDeliveryService->getId());
        $deliveryMethod = $deliveryMethodRepository->ofId($newDeliveryMethod->getId());

        $deliveryService->addDeliveryMethod($deliveryMethod);
        $deliveryServiceRepository->update($deliveryService);

        $deliveryService = $deliveryServiceRepository->ofId($newDeliveryService->getId());
        $deliveryMethod = $deliveryMethodRepository->ofId($newDeliveryMethod->getId());
        $newTariffPlan = TariffPlanFixture::getOneDeactivated($deliveryService, $deliveryMethod, 'test', 'test', false);
        $tariffPlanRepository = $container->get(TariffPlanRepositoryInterface::class);
        $tariffPlanRepository->create($newTariffPlan);

        $tariffPlan = $container->get(FindTariffPlanByIdDeactivatedQueryHandler::class)(
            new FindTariffPlanByIdDeactivatedQuery($newTariffPlan->getId())
        );

        $this->assertNotNull($tariffPlan);
        $this->assertInstanceOf(TariffPlan::class, $tariffPlan);
        $this->assertInstanceOf(DeliveryService::class, $tariffPlan->getDeliveryService());
        $this->assertEquals('test', $tariffPlan->getCode());
        $this->assertEquals('test', $tariffPlan->getName());
        $this->assertFalse($tariffPlan->isActive());
        $this->assertInstanceOf(Uuid::class, $tariffPlan->getId());
        $this->assertNotNull($tariffPlan->getCreatedAt());
        $this->assertNotNull($tariffPlan->getUpdatedAt());
    }
}
