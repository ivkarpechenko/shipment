<?php

namespace App\Tests\Infrastructure\Http\TariffPlan\v1;

use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\TariffPlan\Entity\TariffPlan;
use App\Domain\TariffPlan\Repository\TariffPlanRepositoryInterface;
use App\Tests\Fixture\DeliveryMethod\DeliveryMethodFixture;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\HttpTestCase;

class CreateTariffPlanControllerTest extends HttpTestCase
{
    public function testCreateTariffPlanRoute()
    {
        $container = $this->getContainer();

        $deliveryServiceRepository = $container->get(DeliveryServiceRepositoryInterface::class);
        $deliveryServiceRepository->create(DeliveryServiceFixture::getOne('dellin', 'auto'));

        $deliveryMethodRepository = $container->get(DeliveryMethodRepositoryInterface::class);
        $deliveryMethodRepository->create(DeliveryMethodFixture::getOne('test', 'test'));

        $newDeliveryService = $deliveryServiceRepository->ofCode('dellin');
        $newDeliveryMethod = $deliveryMethodRepository->ofCode('test');

        $newDeliveryService->addDeliveryMethod($newDeliveryMethod);
        $deliveryServiceRepository->update($newDeliveryService);

        $newDeliveryService = $deliveryServiceRepository->ofCode('dellin');
        $newDeliveryMethod = $deliveryMethodRepository->ofCode('test');

        $this->client->request(
            'POST',
            '/api/v1/tariff-plan',
            [
                'deliveryServiceCode' => $newDeliveryService->getCode(),
                'deliveryMethodCode' => $newDeliveryMethod->getCode(),
                'code' => 'auto',
                'name' => 'auto',
            ]
        );

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(201);

        $tariffPlan = $container->get(TariffPlanRepositoryInterface::class)->ofCode(
            $newDeliveryService->getCode(),
            $newDeliveryMethod->getCode(),
            'auto'
        );

        $this->assertNotNull($tariffPlan);
        $this->assertInstanceOf(TariffPlan::class, $tariffPlan);
        $this->assertInstanceOf(DeliveryService::class, $tariffPlan->getDeliveryService());
        $this->assertEquals('dellin', $tariffPlan->getDeliveryService()->getCode());
        $this->assertEquals('test', $tariffPlan->getDeliveryMethod()->getCode());
        $this->assertEquals('auto', $tariffPlan->getCode());
        $this->assertEquals('auto', $tariffPlan->getName());
        $this->assertTrue($tariffPlan->isActive());
        $this->assertNotNull($tariffPlan->getCreatedAt());
        $this->assertNull($tariffPlan->getUpdatedAt());
    }
}
