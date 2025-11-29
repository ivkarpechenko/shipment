<?php

namespace App\Tests\Application\TariffPlan\Query;

use App\Application\Query;
use App\Application\QueryHandler;
use App\Application\TariffPlan\Query\GetTariffPlansByPaginateQuery;
use App\Application\TariffPlan\Query\GetTariffPlansByPaginateQueryHandler;
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

class GetTariffPlansByPaginateQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new GetTariffPlansByPaginateQuery(0, 1)
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(GetTariffPlansByPaginateQueryHandler::class)
        );
    }

    public function testGetTariffPlansByPaginateQueryHandler()
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

        $deliveryService = $deliveryServiceRepository->ofId($newDeliveryService->getId());
        $deliveryMethod = $deliveryMethodRepository->ofId($newDeliveryMethod->getId());
        $newTariffPlan = TariffPlanFixture::getOne($deliveryService, $deliveryMethod, 'test2', 'test2');
        $tariffPlanRepository = $container->get(TariffPlanRepositoryInterface::class);
        $tariffPlanRepository->create($newTariffPlan);

        $tariffPlans = $container->get(GetTariffPlansByPaginateQueryHandler::class)(
            new GetTariffPlansByPaginateQuery(0, 2)
        );

        $this->assertNotEmpty($tariffPlans);
        $this->assertIsArray($tariffPlans);
        $this->assertArrayHasKey('data', $tariffPlans);
        $this->assertArrayHasKey('total', $tariffPlans);
        $this->assertArrayHasKey('pages', $tariffPlans);
        $this->assertCount(2, $tariffPlans['data']);
        $this->assertEquals(2, $tariffPlans['total']);
        $this->assertEquals(1, $tariffPlans['pages']);

        $tariffPlan = reset($tariffPlans['data']);

        $this->assertInstanceOf(TariffPlan::class, $tariffPlan);
        $this->assertInstanceOf(DeliveryService::class, $tariffPlan->getDeliveryService());
        $this->assertInstanceOf(DeliveryMethod::class, $tariffPlan->getDeliveryMethod());
        $this->assertEquals($newDeliveryService->getCode(), $tariffPlan->getDeliveryService()->getCode());
        $this->assertEquals($newDeliveryMethod->getCode(), $tariffPlan->getDeliveryMethod()->getCode());
        $this->assertEquals('test', $tariffPlan->getCode());
        $this->assertEquals('test', $tariffPlan->getName());
        $this->assertTrue($tariffPlan->isActive());
        $this->assertNotNull($tariffPlan->getCreatedAt());
        $this->assertNull($tariffPlan->getUpdatedAt());
    }
}
