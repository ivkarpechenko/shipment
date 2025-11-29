<?php

namespace App\Tests\Application\TariffPlan\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\TariffPlan\Command\CreateTariffPlanCommand;
use App\Application\TariffPlan\Command\CreateTariffPlanCommandHandler;
use App\Domain\DeliveryMethod\Entity\DeliveryMethod;
use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\TariffPlan\Entity\TariffPlan;
use App\Domain\TariffPlan\Exception\TariffPlanIsNotSupportedByDeliveryServiceException;
use App\Domain\TariffPlan\Repository\TariffPlanRepositoryInterface;
use App\Infrastructure\DeliveryService\Dellin\Strategy\DellinTariffPlanStrategy;
use App\Tests\Fixture\DeliveryMethod\DeliveryMethodFixture;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class CreateTariffPlanCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new CreateTariffPlanCommand('dellin', 'courier', 'auto', 'auto')
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(CreateTariffPlanCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new CreateTariffPlanCommand('dellin', 'courier', 'auto', 'auto');
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testCreateTariffPlanCommandHandler()
    {
        $container = $this->getContainer();

        $newDeliveryService = DeliveryServiceFixture::getOne('dellin', 'test');
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

        $container->get(CreateTariffPlanCommandHandler::class)(
            new CreateTariffPlanCommand($deliveryService->getCode(), $deliveryMethod->getCode(), 'auto', 'auto')
        );

        $tariffPlan = $container->get(TariffPlanRepositoryInterface::class)->ofCode(
            $newDeliveryService->getCode(),
            $newDeliveryMethod->getCode(),
            'auto'
        );

        $this->assertNotNull($tariffPlan);
        $this->assertInstanceOf(TariffPlan::class, $tariffPlan);
        $this->assertInstanceOf(DeliveryService::class, $tariffPlan->getDeliveryService());
        $this->assertInstanceOf(DeliveryMethod::class, $tariffPlan->getDeliveryMethod());
        $this->assertEquals($newDeliveryService->getCode(), $tariffPlan->getDeliveryService()->getCode());
        $this->assertEquals($newDeliveryMethod->getCode(), $tariffPlan->getDeliveryMethod()->getCode());
        $this->assertEquals('auto', $tariffPlan->getName());
        $this->assertEquals('auto', $tariffPlan->getCode());
        $this->assertTrue($tariffPlan->isActive());
        $this->assertInstanceOf(Uuid::class, $tariffPlan->getId());
        $this->assertNotNull($tariffPlan->getCreatedAt());
        $this->assertNull($tariffPlan->getUpdatedAt());
    }

    public function testCreateNotAvailabilityTariffPlan()
    {
        $container = $this->getContainer();

        $newDeliveryService = DeliveryServiceFixture::getOne('dellin', 'auto');
        $deliveryServiceRepository = $container->get(DeliveryServiceRepositoryInterface::class);
        $deliveryServiceRepository->create($newDeliveryService);

        $newDeliveryMethod = DeliveryMethodFixture::getOne('courier', 'test');
        $deliveryMethodRepository = $container->get(DeliveryMethodRepositoryInterface::class);
        $deliveryMethodRepository->create($newDeliveryMethod);

        $deliveryService = $deliveryServiceRepository->ofId($newDeliveryService->getId());
        $deliveryMethod = $deliveryMethodRepository->ofId($newDeliveryMethod->getId());

        $deliveryService->addDeliveryMethod($deliveryMethod);
        $deliveryServiceRepository->update($deliveryService);

        $dellinTariffPlanStrategyMock = $this->createMock(DellinTariffPlanStrategy::class);
        $dellinTariffPlanStrategyMock->method('execute')->willReturn(false);

        $container->set(DellinTariffPlanStrategy::class, $dellinTariffPlanStrategyMock);

        $this->expectException(TariffPlanIsNotSupportedByDeliveryServiceException::class);
        $container->get(CreateTariffPlanCommandHandler::class)(
            new CreateTariffPlanCommand($newDeliveryService->getCode(), $newDeliveryMethod->getCode(), 'auto', 'auto')
        );
    }
}
