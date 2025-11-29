<?php

namespace App\Tests\Application\DeliveryService\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\DeliveryService\Command\UpdateDeliveryServiceCommand;
use App\Application\DeliveryService\Command\UpdateDeliveryServiceCommandHandler;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class UpdateDeliveryServiceCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new UpdateDeliveryServiceCommand('test', 'test', true)
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(UpdateDeliveryServiceCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new UpdateDeliveryServiceCommand('test', 'test', true);
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testUpdateDeliveryServiceCommandHandler()
    {
        $container = $this->getContainer();
        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');

        $repository = $container->get(DeliveryServiceRepositoryInterface::class);
        $repository->create($newDeliveryService);

        $container->get(UpdateDeliveryServiceCommandHandler::class)(
            new UpdateDeliveryServiceCommand('test', 'updated test', null)
        );

        $deliveryService = $repository->ofCode('test');

        $this->assertNotNull($deliveryService);
        $this->assertInstanceOf(DeliveryService::class, $deliveryService);
        $this->assertEquals('test', $deliveryService->getCode());
        $this->assertEquals('updated test', $deliveryService->getName());
        $this->assertTrue($deliveryService->isActive());
        $this->assertInstanceOf(Uuid::class, $deliveryService->getId());
        $this->assertNotNull($deliveryService->getCreatedAt());
        $this->assertNotNull($deliveryService->getUpdatedAt());

        $container->get(UpdateDeliveryServiceCommandHandler::class)(
            new UpdateDeliveryServiceCommand('test', 'test', false)
        );

        $deliveryService = $repository->ofCode('test');

        $this->assertNull($deliveryService);

        $deactivatedDeliveryService = $repository->ofCodeDeactivated('test');

        $this->assertNotNull($deactivatedDeliveryService);
        $this->assertInstanceOf(DeliveryService::class, $deactivatedDeliveryService);
        $this->assertEquals('test', $deactivatedDeliveryService->getCode());
        $this->assertEquals('test', $deactivatedDeliveryService->getName());
        $this->assertFalse($deactivatedDeliveryService->isActive());
        $this->assertInstanceOf(Uuid::class, $deactivatedDeliveryService->getId());
        $this->assertNotNull($deactivatedDeliveryService->getCreatedAt());
        $this->assertNotNull($deactivatedDeliveryService->getUpdatedAt());
    }
}
