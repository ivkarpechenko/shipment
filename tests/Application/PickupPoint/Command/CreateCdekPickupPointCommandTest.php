<?php

namespace App\Tests\Application\PickupPoint\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\PickupPoint\Command\CreateCdekPickupPointCommand;
use App\Application\PickupPoint\Command\CreateCdekPickupPointCommandHandler;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\PickupPoint\Entity\PickupPoint;
use App\Domain\PickupPoint\Repository\PickupPointRepositoryInterface;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\Fixture\PickupPoint\CdekPickupPointDtoFixture;
use App\Tests\MessageBusTestCase;

class CreateCdekPickupPointCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new CreateCdekPickupPointCommand(CdekPickupPointDtoFixture::getOne())
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(CreateCdekPickupPointCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new CreateCdekPickupPointCommand(CdekPickupPointDtoFixture::getOne());
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testCreateCdekPickupPointCommandHandler()
    {
        $container = $this->getContainer();

        $newDeliveryService = DeliveryServiceFixture::getOne('cdek', 'test');
        $deliveryServiceRepository = $container->get(DeliveryServiceRepositoryInterface::class);
        $deliveryServiceRepository->create($newDeliveryService);

        $container->get(CreateCdekPickupPointCommandHandler::class)(
            new CreateCdekPickupPointCommand(CdekPickupPointDtoFixture::getOne())
        );

        $deliveryService = $deliveryServiceRepository->ofId($newDeliveryService->getId());

        $pickupPoint = $container->get(PickupPointRepositoryInterface::class)->ofDeliveryServiceAndCode($deliveryService, 'BEYe1');

        $this->assertNotNull($pickupPoint);
        $this->assertInstanceOf(PickupPoint::class, $pickupPoint);
        $this->assertNotNull($pickupPoint->getId());
        $this->assertEquals('BEYe1', $pickupPoint->getName());
        $this->assertEquals('BEYe1', $pickupPoint->getCode());
        $this->assertEquals($deliveryService->getId(), $pickupPoint->getDeliveryService()->getId());
        $this->assertEquals('PickupPoint', $pickupPoint->getType());
        $this->assertNull($pickupPoint->getWeightMax());
        $this->assertNull($pickupPoint->getWeightMin());
        $this->assertNotNull($pickupPoint->getCreatedAt());
        $this->assertNull($pickupPoint->getUpdatedAt());
    }
}
