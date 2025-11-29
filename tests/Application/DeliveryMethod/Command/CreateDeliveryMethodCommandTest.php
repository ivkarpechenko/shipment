<?php

declare(strict_types=1);

namespace App\Tests\Application\DeliveryMethod\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\DeliveryMethod\Command\CreateDeliveryMethodCommand;
use App\Application\DeliveryMethod\Command\CreateDeliveryMethodCommandHandler;
use App\Domain\DeliveryMethod\Entity\DeliveryMethod;
use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class CreateDeliveryMethodCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf(): void
    {
        $this->assertInstanceOf(
            Command::class,
            new CreateDeliveryMethodCommand('test', 'test')
        );

        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(CreateDeliveryMethodCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new CreateDeliveryMethodCommand('test', 'test');
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testCreateDeliveryMethodCommandHandler()
    {
        $container = $this->getContainer();

        $container->get(CreateDeliveryMethodCommandHandler::class)(
            new CreateDeliveryMethodCommand('test', 'test')
        );

        $deliveryMethod = $container->get(DeliveryMethodRepositoryInterface::class)->ofCode('test');

        $this->assertNotNull($deliveryMethod);
        $this->assertInstanceOf(DeliveryMethod::class, $deliveryMethod);
        $this->assertEquals('test', $deliveryMethod->getCode());
        $this->assertEquals('test', $deliveryMethod->getName());
        $this->assertTrue($deliveryMethod->isActive());
        $this->assertInstanceOf(Uuid::class, $deliveryMethod->getId());
        $this->assertNotNull($deliveryMethod->getCreatedAt());
        $this->assertNull($deliveryMethod->getUpdatedAt());
    }
}
