<?php

declare(strict_types=1);

namespace App\Tests\Application\PickupPoint\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\PickupPoint\Command\CreateDellinPickupPointCommand;
use App\Application\PickupPoint\Command\CreateDellinPickupPointCommandHandler;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\PickupPoint\Entity\PickupPoint;
use App\Domain\PickupPoint\Repository\PickupPointRepositoryInterface;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\Fixture\PickupPoint\DellinPickupPointDtoFixture;
use App\Tests\MessageBusTestCase;

class CreateDellinPickupPointCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new CreateDellinPickupPointCommand(DellinPickupPointDtoFixture::getOne())
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(CreateDellinPickupPointCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new CreateDellinPickupPointCommand(DellinPickupPointDtoFixture::getOne());
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testCreateDellinPickupPointCommandHandler()
    {
        $container = $this->getContainer();

        $newDeliveryService = DeliveryServiceFixture::getOne('dellin', 'dellin');
        $deliveryServiceRepository = $container->get(DeliveryServiceRepositoryInterface::class);
        $deliveryServiceRepository->create($newDeliveryService);

        $container->get(CreateDellinPickupPointCommandHandler::class)(
            new CreateDellinPickupPointCommand(DellinPickupPointDtoFixture::getOne())
        );

        $deliveryService = $deliveryServiceRepository->ofId($newDeliveryService->getId());

        $pickupPoint = $container->get(PickupPointRepositoryInterface::class)->ofDeliveryServiceAndCode($deliveryService, 'BEYe1');

        $this->assertNotNull($pickupPoint);
        $this->assertInstanceOf(PickupPoint::class, $pickupPoint);
        $this->assertNotNull($pickupPoint->getId());
        $this->assertEquals('test', $pickupPoint->getName());
        $this->assertEquals('BEYe1', $pickupPoint->getCode());
        $this->assertEquals($deliveryService->getId(), $pickupPoint->getDeliveryService()->getId());
        $this->assertEquals('PickupPoint', $pickupPoint->getType());
        $this->assertNull($pickupPoint->getWeightMax());
        $this->assertNull($pickupPoint->getWeightMin());
        $this->assertNotNull($pickupPoint->getCreatedAt());
        $this->assertNull($pickupPoint->getUpdatedAt());
    }
}
