<?php

namespace App\Tests\Infrastructure\Http\TariffPlan\v1;

use App\Domain\DeliveryMethod\Entity\DeliveryMethod;
use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\TariffPlan\Entity\TariffPlan;
use App\Domain\TariffPlan\Repository\TariffPlanRepositoryInterface;
use App\Tests\Fixture\DeliveryMethod\DeliveryMethodFixture;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\Fixture\TariffPlan\TariffPlanFixture;
use App\Tests\HttpTestCase;

class UpdateTariffPlanControllerTest extends HttpTestCase
{
    public function testUpdateDeliveryServiceName()
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

        $newTariffPlan = TariffPlanFixture::getOne($deliveryService, $deliveryMethod, 'test', 'test tariff');
        $tariffPlanRepository = $container->get(TariffPlanRepositoryInterface::class);
        $tariffPlanRepository->create($newTariffPlan);

        $this->client->request(
            'PUT',
            "/api/v1/tariff-plan/{$newDeliveryService->getCode()}/{$newDeliveryMethod->getCode()}/{$newTariffPlan->getCode()}",
            [
                'name' => 'updated test tariff',
            ]
        );

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(200);

        $tariffPlan = $tariffPlanRepository->ofCode($newDeliveryService->getCode(), $newDeliveryMethod->getCode(), $newTariffPlan->getCode());

        $this->assertNotNull($tariffPlan);
        $this->assertInstanceOf(TariffPlan::class, $tariffPlan);
        $this->assertInstanceOf(DeliveryService::class, $tariffPlan->getDeliveryService());
        $this->assertInstanceOf(DeliveryMethod::class, $tariffPlan->getDeliveryMethod());
        $this->assertEquals('test', $tariffPlan->getCode());
        $this->assertEquals('updated test tariff', $tariffPlan->getName());
        $this->assertTrue($tariffPlan->isActive());
        $this->assertNotNull($tariffPlan->getCreatedAt());
        $this->assertNotNull($tariffPlan->getUpdatedAt());
    }

    public function testUpdateDeliveryServiceIsActive()
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

        $newTariffPlan = TariffPlanFixture::getOne($deliveryService, $deliveryMethod, 'test', 'test tariff');
        $tariffPlanRepository = $container->get(TariffPlanRepositoryInterface::class);
        $tariffPlanRepository->create($newTariffPlan);

        $this->client->request(
            'PUT',
            "/api/v1/tariff-plan/{$newDeliveryService->getCode()}/{$newDeliveryMethod->getCode()}/{$newTariffPlan->getCode()}",
            [
                'isActive' => false,
            ]
        );

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(200);

        $tariffPlan = $tariffPlanRepository->ofCodeDeactivated($newDeliveryService->getCode(), $newDeliveryMethod->getCode(), $newTariffPlan->getCode());

        $this->assertNotNull($tariffPlan);
        $this->assertInstanceOf(TariffPlan::class, $tariffPlan);
        $this->assertInstanceOf(DeliveryService::class, $tariffPlan->getDeliveryService());
        $this->assertEquals('test', $tariffPlan->getCode());
        $this->assertEquals('test tariff', $tariffPlan->getName());
        $this->assertFalse($tariffPlan->isActive());
        $this->assertNotNull($tariffPlan->getCreatedAt());
        $this->assertNotNull($tariffPlan->getUpdatedAt());
    }
}
