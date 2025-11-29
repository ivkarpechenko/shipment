<?php

namespace App\Tests\Domain\TariffPlan\Service;

use App\Domain\DeliveryMethod\Exception\DeliveryMethodDeactivatedException;
use App\Domain\DeliveryMethod\Exception\DeliveryMethodNotFoundException;
use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Exception\DeliveryServiceDeactivatedException;
use App\Domain\DeliveryService\Exception\DeliveryServiceNotFoundException;
use App\Domain\DeliveryService\Exception\DeliveryServiceNotSupportDeliveryMethodException;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\TariffPlan\Entity\TariffPlan;
use App\Domain\TariffPlan\Exception\TariffPlanAlreadyCreatedException;
use App\Domain\TariffPlan\Exception\TariffPlanDeactivatedException;
use App\Domain\TariffPlan\Exception\TariffPlanIsNotSupportedByDeliveryServiceException;
use App\Domain\TariffPlan\Repository\TariffPlanRepositoryInterface;
use App\Domain\TariffPlan\Service\CreateTariffPlanService;
use App\Domain\TariffPlan\Strategy\TariffPlanContext;
use App\Tests\Fixture\DeliveryMethod\DeliveryMethodFixture;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\Fixture\TariffPlan\TariffPlanFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class CreateTariffPlanServiceTest extends KernelTestCase
{
    private DeliveryServiceRepositoryInterface $deliveryServiceRepository;

    private DeliveryMethodRepositoryInterface $deliveryMethodRepository;

    private TariffPlanRepositoryInterface $tariffPlanRepository;

    private TariffPlanContext $tariffPlanContext;

    protected function setUp(): void
    {
        parent::setUp();

        $this->deliveryServiceRepository = $this->createMock(DeliveryServiceRepositoryInterface::class);
        $this->deliveryMethodRepository = $this->createMock(DeliveryMethodRepositoryInterface::class);
        $this->tariffPlanRepository = $this->createMock(TariffPlanRepositoryInterface::class);
        $this->tariffPlanContext = $this->createMock(TariffPlanContext::class);
    }

    public function testCreateTariffPlan()
    {
        $this->tariffPlanContext->method('execute')->willReturn(true);

        $service = new CreateTariffPlanService(
            $this->deliveryMethodRepository,
            $this->deliveryServiceRepository,
            $this->tariffPlanContext,
            $this->tariffPlanRepository,
        );

        $newDeliveryService = DeliveryServiceFixture::getOne(
            'test',
            'test'
        );
        $this->deliveryServiceRepository->method('ofCode')->willReturn($newDeliveryService);

        $newDeliveryMethod = DeliveryMethodFixture::getOne('test', 'test');
        $this->deliveryMethodRepository->method('ofCode')->willReturn($newDeliveryMethod);

        $newDeliveryService->addDeliveryMethod($newDeliveryMethod);

        $service->create($newDeliveryService->getCode(), $newDeliveryMethod->getCode(), 'test', 'test');

        $newTariffPlan = TariffPlanFixture::getOne(
            $newDeliveryService,
            $newDeliveryMethod,
            'test',
            'test'
        );
        $this->tariffPlanRepository->method('ofCode')->willReturn($newTariffPlan);

        $tariffPlan = $this->tariffPlanRepository->ofCode($newDeliveryService->getCode(), $newDeliveryMethod->getCode(), $newTariffPlan->getCode());

        $this->assertNotNull($tariffPlan);
        $this->assertInstanceOf(TariffPlan::class, $tariffPlan);
        $this->assertEquals($newTariffPlan, $tariffPlan);
        $this->assertEquals('test', $tariffPlan->getCode());
        $this->assertEquals('test', $tariffPlan->getName());
        $this->assertTrue($tariffPlan->isActive());
        $this->assertInstanceOf(Uuid::class, $tariffPlan->getId());
        $this->assertInstanceOf(DeliveryService::class, $tariffPlan->getDeliveryService());
        $this->assertEquals($newDeliveryService, $tariffPlan->getDeliveryService());
        $this->assertEquals($newDeliveryMethod, $tariffPlan->getDeliveryMethod());
        $this->assertNotNull($tariffPlan->getCreatedAt());
        $this->assertNull($tariffPlan->getUpdatedAt());
    }

    public function testCreateTariffPlanIfExists()
    {
        $this->tariffPlanContext->method('execute')->willReturn(true);

        $service = new CreateTariffPlanService(
            $this->deliveryMethodRepository,
            $this->deliveryServiceRepository,
            $this->tariffPlanContext,
            $this->tariffPlanRepository,
        );

        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $this->deliveryServiceRepository->method('ofCode')->willReturn($newDeliveryService);

        $newDeliveryMethod = DeliveryMethodFixture::getOne('test', 'test');
        $this->deliveryMethodRepository->method('ofCode')->willReturn($newDeliveryMethod);

        $newDeliveryService->addDeliveryMethod($newDeliveryMethod);

        $this->tariffPlanRepository->method('ofCode')->willReturn(
            TariffPlanFixture::getOne(
                $newDeliveryService,
                $newDeliveryMethod,
                'test',
                'test'
            )
        );

        $this->expectException(TariffPlanAlreadyCreatedException::class);
        $service->create($newDeliveryService->getCode(), $newDeliveryMethod->getCode(), 'test', 'test');
    }

    public function testCreateTariffPlanIfDeliveryServiceDeactivated()
    {
        $this->tariffPlanContext->method('execute')->willReturn(true);

        $service = new CreateTariffPlanService(
            $this->deliveryMethodRepository,
            $this->deliveryServiceRepository,
            $this->tariffPlanContext,
            $this->tariffPlanRepository,
        );

        $newDeliveryService = DeliveryServiceFixture::getOneDeactivated('test', 'test', false);
        $this->deliveryServiceRepository->method('ofCodeDeactivated')->willReturn($newDeliveryService);

        $newDeliveryMethod = DeliveryMethodFixture::getOne('test', 'test');
        $this->deliveryMethodRepository->method('ofCode')->willReturn($newDeliveryMethod);

        $newDeliveryService->addDeliveryMethod($newDeliveryMethod);

        $this->expectException(DeliveryServiceDeactivatedException::class);
        $service->create($newDeliveryService->getCode(), $newDeliveryMethod->getCode(), 'test', 'test');
    }

    public function testCreateTariffPlanIfDeliveryServiceNotExist()
    {
        $this->tariffPlanContext->method('execute')->willReturn(true);

        $service = new CreateTariffPlanService(
            $this->deliveryMethodRepository,
            $this->deliveryServiceRepository,
            $this->tariffPlanContext,
            $this->tariffPlanRepository,
        );

        $this->deliveryServiceRepository->method('ofCode')->willReturn(null);

        $newDeliveryMethod = DeliveryMethodFixture::getOne('test', 'test');
        $this->deliveryMethodRepository->method('ofCode')->willReturn($newDeliveryMethod);

        $this->expectException(DeliveryServiceNotFoundException::class);
        $service->create('test', $newDeliveryMethod->getCode(), 'test', 'test');
    }

    public function testCreateTariffPlanIfAlreadyCreated()
    {
        $this->tariffPlanContext->method('execute')->willReturn(true);

        $service = new CreateTariffPlanService(
            $this->deliveryMethodRepository,
            $this->deliveryServiceRepository,
            $this->tariffPlanContext,
            $this->tariffPlanRepository,
        );

        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $this->deliveryServiceRepository->method('ofCode')->willReturn($newDeliveryService);

        $newDeliveryMethod = DeliveryMethodFixture::getOne('test', 'test');
        $this->deliveryMethodRepository->method('ofCode')->willReturn($newDeliveryMethod);

        $newDeliveryService->addDeliveryMethod($newDeliveryMethod);

        $tariffPlan = TariffPlanFixture::getOne($newDeliveryService, $newDeliveryMethod, 'test', 'test');
        $this->tariffPlanRepository->method('ofCode')->willReturn($tariffPlan);

        $this->expectException(TariffPlanAlreadyCreatedException::class);
        $service->create($newDeliveryService->getCode(), $newDeliveryMethod->getCode(), 'test', 'test');
    }

    public function testCreateTariffPlanIfDeactivated()
    {
        $this->tariffPlanContext->method('execute')->willReturn(true);

        $service = new CreateTariffPlanService(
            $this->deliveryMethodRepository,
            $this->deliveryServiceRepository,
            $this->tariffPlanContext,
            $this->tariffPlanRepository,
        );

        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $this->deliveryServiceRepository->method('ofCode')->willReturn($newDeliveryService);

        $newDeliveryMethod = DeliveryMethodFixture::getOne('test', 'test');
        $this->deliveryMethodRepository->method('ofCode')->willReturn($newDeliveryMethod);

        $newDeliveryService->addDeliveryMethod($newDeliveryMethod);

        $tariffPlan = TariffPlanFixture::getOneDeactivated($newDeliveryService, $newDeliveryMethod, 'test', 'test', false);
        $this->tariffPlanRepository->method('ofCodeDeactivated')->willReturn($tariffPlan);

        $this->expectException(TariffPlanDeactivatedException::class);
        $service->create($newDeliveryService->getCode(), $newDeliveryMethod->getCode(), 'test', 'test');
    }

    public function testCreateTariffPlanIfNotSupported()
    {
        $this->tariffPlanContext->method('execute')->willReturn(false);

        $service = new CreateTariffPlanService(
            $this->deliveryMethodRepository,
            $this->deliveryServiceRepository,
            $this->tariffPlanContext,
            $this->tariffPlanRepository,
        );

        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $this->deliveryServiceRepository->method('ofCode')->willReturn($newDeliveryService);

        $newDeliveryMethod = DeliveryMethodFixture::getOne('test', 'test');
        $this->deliveryMethodRepository->method('ofCode')->willReturn($newDeliveryMethod);

        $newDeliveryService->addDeliveryMethod($newDeliveryMethod);

        $this->expectException(TariffPlanIsNotSupportedByDeliveryServiceException::class);
        $service->create($newDeliveryService->getCode(), $newDeliveryMethod->getCode(), 'test', 'test');
    }

    public function testCreateTariffPlanIfDeliveryMethodDeactivated()
    {
        $this->tariffPlanContext->method('execute')->willReturn(true);

        $service = new CreateTariffPlanService(
            $this->deliveryMethodRepository,
            $this->deliveryServiceRepository,
            $this->tariffPlanContext,
            $this->tariffPlanRepository,
        );

        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $this->deliveryServiceRepository->method('ofCode')->willReturn($newDeliveryService);

        $newDeliveryMethod = DeliveryMethodFixture::getOneDeactivated('test', 'test', false);
        $this->deliveryMethodRepository->method('ofCodeDeactivated')->willReturn($newDeliveryMethod);

        $newDeliveryService->addDeliveryMethod($newDeliveryMethod);

        $this->expectException(DeliveryMethodDeactivatedException::class);
        $service->create($newDeliveryService->getCode(), $newDeliveryMethod->getCode(), 'test', 'test');
    }

    public function testCreateTariffPlanIfDeliveryMethodNotExist()
    {
        $this->tariffPlanContext->method('execute')->willReturn(true);

        $service = new CreateTariffPlanService(
            $this->deliveryMethodRepository,
            $this->deliveryServiceRepository,
            $this->tariffPlanContext,
            $this->tariffPlanRepository,
        );

        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $this->deliveryServiceRepository->method('ofCode')->willReturn($newDeliveryService);

        $this->deliveryMethodRepository->method('ofCode')->willReturn(null);

        $this->expectException(DeliveryMethodNotFoundException::class);
        $service->create($newDeliveryService->getCode(), 'test', 'test', 'test');
    }

    public function testCreateTariffPlanIfDeliveryServiceMethodNotExist()
    {
        $this->tariffPlanContext->method('execute')->willReturn(true);

        $service = new CreateTariffPlanService(
            $this->deliveryMethodRepository,
            $this->deliveryServiceRepository,
            $this->tariffPlanContext,
            $this->tariffPlanRepository,
        );

        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $this->deliveryServiceRepository->method('ofCode')->willReturn($newDeliveryService);

        $newDeliveryMethod = DeliveryMethodFixture::getOne('test', 'test');
        $this->deliveryMethodRepository->method('ofCode')->willReturn($newDeliveryMethod);

        $this->expectException(DeliveryServiceNotSupportDeliveryMethodException::class);
        $service->create($newDeliveryService->getCode(), $newDeliveryMethod->getCode(), 'test', 'test');
    }
}
