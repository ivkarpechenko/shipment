<?php

namespace App\Tests\Application\PickupPoint\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\PickupPoint\Command\CreatePickupPointCommand;
use App\Application\PickupPoint\Command\CreatePickupPointCommandHandler;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\PickupPoint\Entity\PickupPoint;
use App\Domain\PickupPoint\Repository\PickupPointRepositoryInterface;
use App\Infrastructure\DBAL\Repository\Doctrine\PickupPoint\DoctrinePickupPointRepository;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\Fixture\PickupPoint\PickupPointDtoFixture;
use App\Tests\Fixture\PickupPoint\PickupPointFixture;
use App\Tests\MessageBusTestCase;

class CreatePickupPointCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');

        $this->assertInstanceOf(
            Command::class,
            new CreatePickupPointCommand(PickupPointDtoFixture::getOne($deliveryService))
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(CreatePickupPointCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $deliveryService = DeliveryServiceFixture::getOne('test', 'test');

        $command = new CreatePickupPointCommand(PickupPointDtoFixture::getOne($deliveryService));
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testCreatePickupPointCommandHandler()
    {
        $container = $this->getContainer();

        $newDeliveryService = DeliveryServiceFixture::getOne('dellin', 'test');
        $deliveryServiceRepository = $container->get(DeliveryServiceRepositoryInterface::class);
        $deliveryServiceRepository->create($newDeliveryService);

        $deliveryService = $deliveryServiceRepository->ofId($newDeliveryService->getId());

        $container->get(CreatePickupPointCommandHandler::class)(
            new CreatePickupPointCommand(PickupPointDtoFixture::getOne(
                deliveryService: $deliveryService,
                name: 'test pickup point'
            ))
        );

        $pickupPoint = $container->get(PickupPointRepositoryInterface::class)->ofDeliveryServiceAndCode($deliveryService, 'BEYe1');

        $this->assertNotNull($pickupPoint);
        $this->assertInstanceOf(PickupPoint::class, $pickupPoint);
        $this->assertNotNull($pickupPoint->getId());
        $this->assertEquals('BEYe1', $pickupPoint->getCode());
        $this->assertEquals($deliveryService->getId(), $pickupPoint->getDeliveryService()->getId());
        $this->assertEquals('PickupPoint', $pickupPoint->getType());
        $this->assertNull($pickupPoint->getWeightMax());
        $this->assertNull($pickupPoint->getWeightMin());
        $this->assertNotNull($pickupPoint->getCreatedAt());
        $this->assertNull($pickupPoint->getUpdatedAt());
        $this->assertEquals('test pickup point', $pickupPoint->getName());
    }

    public function testCreateExistPickupPointCommandHandler()
    {
        $container = $this->getContainer();

        $newDeliveryService = DeliveryServiceFixture::getOne('dellin', 'test');
        $deliveryServiceRepository = $container->get(DeliveryServiceRepositoryInterface::class);
        $deliveryServiceRepository->create($newDeliveryService);

        $deliveryService = $deliveryServiceRepository->ofId($newDeliveryService->getId());

        $PickupPointRepository = $this->getContainer()->get(DoctrinePickupPointRepository::class);
        $PickupPointRepository->create(PickupPointFixture::getOne($deliveryService));

        $deliveryService = $deliveryServiceRepository->ofId($newDeliveryService->getId());
        $container->get(CreatePickupPointCommandHandler::class)(
            new CreatePickupPointCommand(PickupPointDtoFixture::getOne($deliveryService, type: 'tttt'))
        );

        $pickupPoint = $container->get(PickupPointRepositoryInterface::class)->ofDeliveryServiceAndCode($deliveryService, 'BEYe1');

        $this->assertNotNull($pickupPoint);
        $this->assertInstanceOf(PickupPoint::class, $pickupPoint);
        $this->assertNotNull($pickupPoint->getId());
        $this->assertEquals('BEYe1', $pickupPoint->getCode());
        $this->assertEquals($deliveryService->getId(), $pickupPoint->getDeliveryService()->getId());
        $this->assertEquals('tttt', $pickupPoint->getType());
        $this->assertNull($pickupPoint->getWeightMax());
        $this->assertNull($pickupPoint->getWeightMin());
        $this->assertNotNull($pickupPoint->getCreatedAt());
        $this->assertNotNull($pickupPoint->getUpdatedAt());
    }
}
