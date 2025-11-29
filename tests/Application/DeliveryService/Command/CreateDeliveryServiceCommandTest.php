<?php

namespace App\Tests\Application\DeliveryService\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\DeliveryService\Command\CreateDeliveryServiceCommand;
use App\Application\DeliveryService\Command\CreateDeliveryServiceCommandHandler;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class CreateDeliveryServiceCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new CreateDeliveryServiceCommand('test', 'test')
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(CreateDeliveryServiceCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new CreateDeliveryServiceCommand('test', 'test');
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testCreateDeliveryServiceCommandHandler()
    {
        $container = $this->getContainer();

        $container->get(CreateDeliveryServiceCommandHandler::class)(
            new CreateDeliveryServiceCommand('test', 'test')
        );

        $deliveryService = $container->get(DeliveryServiceRepositoryInterface::class)->ofCode('test');

        $this->assertNotNull($deliveryService);
        $this->assertInstanceOf(DeliveryService::class, $deliveryService);
        $this->assertEquals('test', $deliveryService->getCode());
        $this->assertEquals('test', $deliveryService->getName());
        $this->assertTrue($deliveryService->isActive());
        $this->assertInstanceOf(Uuid::class, $deliveryService->getId());
        $this->assertNotNull($deliveryService->getCreatedAt());
        $this->assertNull($deliveryService->getUpdatedAt());
    }
}
