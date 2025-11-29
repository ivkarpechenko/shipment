<?php

namespace App\Tests\Application\Shipment\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Shipment\Command\CreateCalculateCommand;
use App\Application\Shipment\Command\CreateCalculateCommandHandler;
use App\Domain\Address\Repository\AddressRepositoryInterface;
use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Domain\Shipment\Entity\Calculate;
use App\Domain\Shipment\Entity\Shipment;
use App\Domain\Shipment\Repository\ShipmentRepositoryInterface;
use App\Domain\Shipment\Service\CheckAddressInRestrictedAreaService;
use App\Domain\Shipment\Strategy\CalculateContext;
use App\Domain\TariffPlan\Entity\TariffPlan;
use App\Domain\TariffPlan\Repository\TariffPlanRepositoryInterface;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Contact\ContactFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\Fixture\DeliveryMethod\DeliveryMethodFixture;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\Fixture\Shipment\CalculateDtoFixture;
use App\Tests\Fixture\Shipment\ShipmentFixture;
use App\Tests\Fixture\TariffPlan\TariffPlanFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class CreateCalculateCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new CreateCalculateCommand(Uuid::v1(), 'test', 'test', null)
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(CreateCalculateCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new CreateCalculateCommand(Uuid::v1(), 'test', 'test', null);
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testCreateCalculateCommandHandler()
    {
        $container = $this->getContainer();
        $newShipment = $this->createShipment($container);
        $newTariffPlan = $this->createTariffPlan($container);

        $shipment = $container->get(ShipmentRepositoryInterface::class)->ofId($newShipment->getId());
        $tariffPlan = $container->get(TariffPlanRepositoryInterface::class)->ofId($newTariffPlan->getId());

        $calculateContextMock = $this->createMock(CalculateContext::class);
        $calculateContextMock
            ->method('execute')
            ->willReturn(CalculateDtoFixture::getOne(
                1,
                1,
                100.0,
                120.0,
                20.0
            ));

        $container->set(CalculateContext::class, $calculateContextMock);

        $checkAddressInRestrictedAreaServiceMock = $this->createMock(CheckAddressInRestrictedAreaService::class);
        $checkAddressInRestrictedAreaServiceMock->method('check')->willReturn(false);
        $container->set(CheckAddressInRestrictedAreaService::class, $checkAddressInRestrictedAreaServiceMock);

        $calculates = $container->get(CreateCalculateCommandHandler::class)(
            new CreateCalculateCommand(
                $shipment->getId(),
                $tariffPlan->getDeliveryService()->getCode(),
                $tariffPlan->getDeliveryMethod()->getCode(),
                null
            )
        );

        $calculate = reset($calculates);
        $this->assertNotNull($calculate);
        $this->assertInstanceOf(Calculate::class, $calculate);
        $this->assertInstanceOf(Shipment::class, $calculate->getShipment());
        $this->assertInstanceOf(TariffPlan::class, $calculate->getTariffPlan());
        $this->assertEquals(1, $calculate->getMinPeriod());
        $this->assertEquals(1, $calculate->getMaxPeriod());
        $this->assertEquals(100.0, $calculate->getDeliveryCost());
        $this->assertEquals(120.0, $calculate->getDeliveryTotalCost());
        $this->assertEquals(20.0, $calculate->getDeliveryTotalCostTax());
        $this->assertNotNull($calculate->getCreatedAt());
        $this->assertNotNull($calculate->getExpiredAt());
        $this->assertEquals(
            (new \DateTime('+1 hour'))->format('Y-m-d H:i'),
            $calculate->getExpiredAt()->format('Y-m-d H:i')
        );
    }

    protected function createShipment($container)
    {
        $countryRepository = $container->get(CountryRepositoryInterface::class);
        $countryRepository->create(CountryFixture::getOne('Russia', 'RU'));

        $regionRepository = $container->get(RegionRepositoryInterface::class);
        $regionRepository->create(
            RegionFixture::getOne(
                $countryRepository->ofCode('RU'),
                'Moscow',
                'msk'
            )
        );

        $cityRepository = $container->get(CityRepositoryInterface::class);
        $cityRepository->create(
            CityFixture::getOne(
                $regionRepository->ofCode('msk'),
                'city',
                'Moscow'
            )
        );

        $addressRepository = $container->get(AddressRepositoryInterface::class);
        $addressRepository->create(AddressFixture::getOneFilled(
            city: $cityRepository->ofTypeAndName('city', 'Moscow'),
            address: 'address'
        ));

        $contactRepository = $container->get(ContactRepositoryInterface::class);
        $contactRepository->create(ContactFixture::getOne('test@gmail.com', 'contact'));

        $currencyRepository = $container->get(CurrencyRepositoryInterface::class);
        $currencyRepository->create(CurrencyFixture::getOne('RUB', 810, 'Russian ruble'));

        $newShipment = ShipmentFixture::getOne(
            $addressRepository->ofAddress('address'),
            $addressRepository->ofAddress('address'),
            $contactRepository->ofEmail('test@gmail.com'),
            $contactRepository->ofEmail('test@gmail.com'),
            $currencyRepository->ofCode('RUB'),
            new \DateTime('now'),
            new \DateTime('now'),
            new \DateTime('now')
        );
        $container->get(ShipmentRepositoryInterface::class)->create($newShipment);

        return $container->get(ShipmentRepositoryInterface::class)->ofId($newShipment->getId());
    }

    protected function createTariffPlan($container): TariffPlan
    {
        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test');
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

        $newTariffPlan = TariffPlanFixture::getOne($deliveryService, $deliveryMethod, 'test', 'test');
        $tariffPlanRepository = $container->get(TariffPlanRepositoryInterface::class);
        $tariffPlanRepository->create($newTariffPlan);

        return $tariffPlanRepository->ofId($newTariffPlan->getId());
    }
}
