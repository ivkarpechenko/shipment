<?php

namespace App\Tests\Application\TariffPlan\Query;

use App\Application\Query;
use App\Application\QueryHandler;
use App\Application\TariffPlan\Query\GetAllTariffPlansQuery;
use App\Application\TariffPlan\Query\GetAllTariffPlansQueryHandler;
use App\Domain\DeliveryMethod\Entity\DeliveryMethod;
use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\TariffPlan\Entity\TariffPlan;
use App\Domain\TariffPlan\Repository\TariffPlanRepositoryInterface;
use App\Tests\Fixture\DeliveryMethod\DeliveryMethodFixture;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\Fixture\TariffPlan\TariffPlanFixture;
use App\Tests\MessageBusTestCase;

class GetAllTariffPlansQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new GetAllTariffPlansQuery()
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(GetAllTariffPlansQueryHandler::class)
        );
    }

    public function testGetAllTariffPlansQueryHandler()
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
        $newTariffPlan = TariffPlanFixture::getOne($deliveryService, $deliveryMethod, 'test', 'test');
        $tariffPlanRepository = $container->get(TariffPlanRepositoryInterface::class);
        $tariffPlanRepository->create($newTariffPlan);

        $tariffPlans = $container->get(GetAllTariffPlansQueryHandler::class)(
            new GetAllTariffPlansQuery()
        );

        $this->assertNotEmpty($tariffPlans);
        $this->assertIsArray($tariffPlans);

        $tariffPlan = reset($tariffPlans);

        $this->assertInstanceOf(TariffPlan::class, $tariffPlan);
        $this->assertInstanceOf(DeliveryService::class, $tariffPlan->getDeliveryService());
        $this->assertInstanceOf(DeliveryMethod::class, $tariffPlan->getDeliveryMethod());
        $this->assertEquals('test', $tariffPlan->getCode());
        $this->assertEquals('test', $tariffPlan->getName());
        $this->assertTrue($tariffPlan->isActive());
        $this->assertNotNull($tariffPlan->getCreatedAt());
        $this->assertNull($tariffPlan->getUpdatedAt());
    }
}
