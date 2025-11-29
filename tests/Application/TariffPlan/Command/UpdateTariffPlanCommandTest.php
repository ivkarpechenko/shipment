<?php

namespace App\Tests\Application\TariffPlan\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\TariffPlan\Command\UpdateTariffPlanCommand;
use App\Application\TariffPlan\Command\UpdateTariffPlanCommandHandler;
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
use Symfony\Component\Uid\Uuid;

class UpdateTariffPlanCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new UpdateTariffPlanCommand('test', 'test', 'test', 'test', true)
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(UpdateTariffPlanCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new UpdateTariffPlanCommand('test', 'test', 'test', 'test', true);
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testUpdateTariffPlanCommandHandler()
    {
        $container = $this->getContainer();

        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $deliveryServiceRepository = $container->get(DeliveryServiceRepositoryInterface::class);
        $deliveryServiceRepository->create($newDeliveryService);

        $newDeliveryMethod = DeliveryMethodFixture::getOne('courier', 'test');
        $deliveryMethodRepository = $container->get(DeliveryMethodRepositoryInterface::class);
        $deliveryMethodRepository->create($newDeliveryMethod);

        $deliveryService = $deliveryServiceRepository->ofId($newDeliveryService->getId());
        $deliveryMethod = $deliveryMethodRepository->ofId($newDeliveryMethod->getId());

        $newTariffPlan = TariffPlanFixture::getOne($deliveryService, $deliveryMethod, 'test', 'test');
        $tariffPlanRepository = $container->get(TariffPlanRepositoryInterface::class);
        $tariffPlanRepository->create($newTariffPlan);

        $container->get(UpdateTariffPlanCommandHandler::class)(
            new UpdateTariffPlanCommand($newDeliveryService->getCode(), $newDeliveryMethod->getCode(), $newTariffPlan->getCode(), 'updated test', null)
        );

        $tariffPlan = $tariffPlanRepository->ofCode($newDeliveryService->getCode(), $newDeliveryMethod->getCode(), $newTariffPlan->getCode());

        $this->assertNotNull($tariffPlan);
        $this->assertInstanceOf(TariffPlan::class, $tariffPlan);
        $this->assertInstanceOf(DeliveryService::class, $tariffPlan->getDeliveryService());
        $this->assertInstanceOf(DeliveryMethod::class, $tariffPlan->getDeliveryMethod());

        $this->assertEquals('test', $tariffPlan->getCode());
        $this->assertEquals('updated test', $tariffPlan->getName());
        $this->assertTrue($tariffPlan->isActive());
        $this->assertInstanceOf(Uuid::class, $tariffPlan->getId());
        $this->assertNotNull($tariffPlan->getCreatedAt());
        $this->assertNotNull($tariffPlan->getUpdatedAt());

        $container->get(UpdateTariffPlanCommandHandler::class)(
            new UpdateTariffPlanCommand($newDeliveryService->getCode(), $newDeliveryMethod->getCode(), $newTariffPlan->getCode(), 'updated test', false)
        );

        $tariffPlan = $tariffPlanRepository->ofCode($newDeliveryService->getCode(), $newDeliveryMethod->getCode(), $newTariffPlan->getCode());

        $this->assertNull($tariffPlan);

        $deactivatedTariffPlan = $tariffPlanRepository->ofCodeDeactivated($newDeliveryService->getCode(), $newDeliveryMethod->getCode(), $newTariffPlan->getCode());

        $this->assertNotNull($deactivatedTariffPlan);
        $this->assertInstanceOf(TariffPlan::class, $deactivatedTariffPlan);
        $this->assertInstanceOf(DeliveryService::class, $deactivatedTariffPlan->getDeliveryService());
        $this->assertInstanceOf(DeliveryMethod::class, $deactivatedTariffPlan->getDeliveryMethod());
        $this->assertEquals('test', $deactivatedTariffPlan->getCode());
        $this->assertEquals('updated test', $deactivatedTariffPlan->getName());
        $this->assertFalse($deactivatedTariffPlan->isActive());
        $this->assertInstanceOf(Uuid::class, $deactivatedTariffPlan->getId());
        $this->assertNotNull($deactivatedTariffPlan->getCreatedAt());
        $this->assertNotNull($deactivatedTariffPlan->getUpdatedAt());
    }
}
