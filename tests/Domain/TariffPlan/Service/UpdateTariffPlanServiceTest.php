<?php

namespace App\Tests\Domain\TariffPlan\Service;

use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\TariffPlan\Entity\TariffPlan;
use App\Domain\TariffPlan\Repository\TariffPlanRepositoryInterface;
use App\Domain\TariffPlan\Service\UpdateTariffPlanService;
use App\Tests\Fixture\DeliveryMethod\DeliveryMethodFixture;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\Fixture\TariffPlan\TariffPlanFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class UpdateTariffPlanServiceTest extends KernelTestCase
{
    private TariffPlanRepositoryInterface $tariffPlanRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tariffPlanRepository = $this->createMock(TariffPlanRepositoryInterface::class);
    }

    public function testUpdateNameTariffPlan()
    {
        $service = new UpdateTariffPlanService($this->tariffPlanRepository);
        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $newDeliveryMethod = DeliveryMethodFixture::getOne('test', 'test');

        $this->tariffPlanRepository->method('ofCode')->willReturn(
            TariffPlanFixture::getOne($newDeliveryService, $newDeliveryMethod, 'test', 'test')
        );

        $service->update($newDeliveryService->getCode(), $newDeliveryMethod->getCode(), 'test', 'updated test', null);

        $this->tariffPlanRepository->method('ofCode')->willReturn(
            TariffPlanFixture::getOne($newDeliveryService, $newDeliveryMethod, 'test', 'test updated')
        );

        $tariffPlan = $this->tariffPlanRepository->ofCode($newDeliveryService->getCode(), $newDeliveryMethod->getCode(), 'test');

        $this->assertNotNull($tariffPlan);
        $this->assertInstanceOf(TariffPlan::class, $tariffPlan);
        $this->assertInstanceOf(DeliveryService::class, $tariffPlan->getDeliveryService());
        $this->assertEquals($newDeliveryService, $tariffPlan->getDeliveryService());
        $this->assertEquals($newDeliveryMethod, $tariffPlan->getDeliveryMethod());
        $this->assertEquals('test', $tariffPlan->getCode());
        $this->assertEquals('updated test', $tariffPlan->getName());
        $this->assertTrue($tariffPlan->isActive());
        $this->assertInstanceOf(Uuid::class, $tariffPlan->getId());
        $this->assertNotNull($tariffPlan->getCreatedAt());
        $this->assertNotNull($tariffPlan->getUpdatedAt());
    }

    public function testUpdateIsActiveTariffPlan()
    {
        $service = new UpdateTariffPlanService($this->tariffPlanRepository);
        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $newDeliveryMethod = DeliveryMethodFixture::getOne('test', 'test');

        $this->tariffPlanRepository->method('ofCode')->willReturn(
            TariffPlanFixture::getOne($newDeliveryService, $newDeliveryMethod, 'test', 'test')
        );

        $service->update($newDeliveryService->getCode(), $newDeliveryMethod->getCode(), 'test', null, false);

        $this->tariffPlanRepository->method('ofCodeDeactivated')->willReturn(
            TariffPlanFixture::getOneDeactivated($newDeliveryService, $newDeliveryMethod, 'test', 'test', false)
        );

        $tariffPlan = $this->tariffPlanRepository->ofCodeDeactivated($newDeliveryService->getCode(), $newDeliveryMethod->getCode(), 'test');

        $this->assertNotNull($tariffPlan);
        $this->assertInstanceOf(TariffPlan::class, $tariffPlan);
        $this->assertInstanceOf(DeliveryService::class, $tariffPlan->getDeliveryService());
        $this->assertEquals($newDeliveryService, $tariffPlan->getDeliveryService());
        $this->assertEquals($newDeliveryMethod, $tariffPlan->getDeliveryMethod());
        $this->assertEquals('test', $tariffPlan->getCode());
        $this->assertEquals('test', $tariffPlan->getName());
        $this->assertFalse($tariffPlan->isActive());
        $this->assertInstanceOf(Uuid::class, $tariffPlan->getId());
        $this->assertNotNull($tariffPlan->getCreatedAt());
        $this->assertNotNull($tariffPlan->getUpdatedAt());
    }
}
