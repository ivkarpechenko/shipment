<?php

declare(strict_types=1);

namespace App\Tests\Application\DeliveryService\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\DeliveryService\Command\UpdateDeliveryServiceRestrictPackageCommand;
use App\Application\DeliveryService\Command\UpdateDeliveryServiceRestrictPackageCommandHandler;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Entity\DeliveryServiceRestrictPackage;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\DeliveryService\Repository\DeliveryServiceRestrictPackageRepositoryInterface;
use App\Tests\Fixture\DeliveryService\DeliverServiceRestrictPackageFixture;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class UpdateDeliveryServiceRestrictPackageCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf(): void
    {
        $this->assertInstanceOf(
            Command::class,
            new UpdateDeliveryServiceRestrictPackageCommand(Uuid::v1(), 10, 20, 30, 40, true)
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(UpdateDeliveryServiceRestrictPackageCommandHandler::class)
        );
    }

    public function testCommandDispatch(): void
    {
        $command = new UpdateDeliveryServiceRestrictPackageCommand(Uuid::v1(), 10, 20, 30, 40, true);
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testUpdateDeliveryServiceRestrictPackageCommandHandler(): void
    {
        $container = $this->getContainer();

        $deliveryServiceRepository = $container->get(DeliveryServiceRepositoryInterface::class);
        $deliveryServiceRestrictPackageRepository = $container->get(DeliveryServiceRestrictPackageRepositoryInterface::class);

        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
        $deliveryServiceRepository->create($newDeliveryService);
        $deliveryService = $deliveryServiceRepository->ofId($newDeliveryService->getId());

        $newDeliveryServiceRestrictPackage = DeliverServiceRestrictPackageFixture::getOne(
            $deliveryService,
            10,
            20,
            30,
            40
        );

        $deliveryServiceRestrictPackageRepository->create($newDeliveryServiceRestrictPackage);

        $container->get(UpdateDeliveryServiceRestrictPackageCommandHandler::class)(
            new UpdateDeliveryServiceRestrictPackageCommand($newDeliveryServiceRestrictPackage->getId(), 10, 20, 30, 40, true)
        );

        $deliveryServiceRestrictPackage = $deliveryServiceRestrictPackageRepository->ofId($newDeliveryServiceRestrictPackage->getId());

        $this->assertNotNull($deliveryServiceRestrictPackage);
        $this->assertInstanceOf(DeliveryServiceRestrictPackage::class, $deliveryServiceRestrictPackage);
        $this->assertInstanceOf(DeliveryService::class, $deliveryServiceRestrictPackage->getDeliveryService());
        $this->assertEquals(10, $deliveryServiceRestrictPackage->getMaxWeight());
        $this->assertEquals(20, $deliveryServiceRestrictPackage->getMaxWidth());
        $this->assertEquals(30, $deliveryServiceRestrictPackage->getMaxHeight());
        $this->assertEquals(40, $deliveryServiceRestrictPackage->getMaxLength());
        $this->assertTrue($deliveryServiceRestrictPackage->isActive());
        $this->assertInstanceOf(Uuid::class, $deliveryServiceRestrictPackage->getId());
        $this->assertNotNull($deliveryServiceRestrictPackage->getCreatedAt());
        $this->assertNotNull($deliveryServiceRestrictPackage->getUpdatedAt());
    }
}
