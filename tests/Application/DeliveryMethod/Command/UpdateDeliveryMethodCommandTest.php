<?php

declare(strict_types=1);

namespace App\Tests\Application\DeliveryMethod\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\DeliveryMethod\Command\UpdateDeliveryMethodCommand;
use App\Application\DeliveryMethod\Command\UpdateDeliveryMethodCommandHandler;
use App\Domain\DeliveryMethod\Entity\DeliveryMethod;
use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;
use App\Tests\Fixture\DeliveryMethod\DeliveryMethodFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class UpdateDeliveryMethodCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceof(): void
    {
        $this->assertInstanceOf(
            Command::class,
            new UpdateDeliveryMethodCommand('test', 'test', true)
        );

        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(UpdateDeliveryMethodCommandHandler::class)
        );
    }

    public function testDispatch(): void
    {
        $command = new UpdateDeliveryMethodCommand('test', 'test', false);
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testUpdateDeliveryMethodCommandHandler()
    {
        $container = $this->getContainer();
        $newDeliveryService = DeliveryMethodFixture::getOne('test', 'test');

        $repository = $container->get(DeliveryMethodRepositoryInterface::class);
        $repository->create($newDeliveryService);

        $container->get(UpdateDeliveryMethodCommandHandler::class)(
            new UpdateDeliveryMethodCommand('test', 'updated test', null)
        );

        $deliveryMethod = $repository->ofCode('test');

        $this->assertNotNull($deliveryMethod);
        $this->assertInstanceOf(DeliveryMethod::class, $deliveryMethod);
        $this->assertEquals('test', $deliveryMethod->getCode());
        $this->assertEquals('updated test', $deliveryMethod->getName());
        $this->assertTrue($deliveryMethod->isActive());
        $this->assertInstanceOf(Uuid::class, $deliveryMethod->getId());
        $this->assertNotNull($deliveryMethod->getCreatedAt());
        $this->assertNotNull($deliveryMethod->getUpdatedAt());
    }
}
