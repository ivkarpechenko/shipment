<?php

namespace App\Tests\Application\Shipment\Command;

use App\Application\Address\Query\External\FindExternalAddressInterface;
use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Shipment\Command\BulkCreateShipmentCommand;
use App\Application\Shipment\Command\BulkCreateShipmentCommandHandler;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\DeliveryService\Repository\DeliveryServiceRestrictPackageRepositoryInterface;
use App\Domain\Shipment\Entity\Shipment;
use App\Domain\Shipment\Repository\CargoRestrictionRepositoryInterface;
use App\Domain\Shipment\Repository\CargoTypeRepositoryInterface;
use App\Domain\Shipment\Service\CheckAddressInRestrictedAreaService;
use App\Domain\TariffPlan\Repository\TariffPlanRepositoryInterface;
use App\Tests\Fixture\Address\AddressDtoFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\Fixture\DeliveryMethod\DeliveryMethodFixture;
use App\Tests\Fixture\DeliveryService\DeliverServiceRestrictPackageFixture;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\Fixture\Shipment\BulkCreateShipmentDtoFixture;
use App\Tests\Fixture\Shipment\CargoRestrictionDtoFixture;
use App\Tests\Fixture\Shipment\CargoTypeFixture;
use App\Tests\Fixture\Shipment\ContactDtoFixture;
use App\Tests\Fixture\Shipment\ProductDtoFixture;
use App\Tests\Fixture\Shipment\StoreDtoFixture;
use App\Tests\Fixture\TariffPlan\TariffPlanFixture;
use App\Tests\MessageBusTestCase;

class BulkCreateShipmentCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new BulkCreateShipmentCommand(BulkCreateShipmentDtoFixture::getOneFilled())
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(BulkCreateShipmentCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new BulkCreateShipmentCommand(BulkCreateShipmentDtoFixture::getOneFilled());
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testBulkCreateShipmentCommandHandler()
    {
        $container = $this->getContainer();

        $addressDto = AddressDtoFixture::getOneFilled();
        $findExternalAddressMock = $this->createMock(FindExternalAddressInterface::class);
        $findExternalAddressMock
            ->method('find')
            ->willReturn($addressDto);

        $container->set(FindExternalAddressInterface::class, $findExternalAddressMock);

        $checkAddressInRestrictedAreaServiceMock = $this->createMock(CheckAddressInRestrictedAreaService::class);
        $checkAddressInRestrictedAreaServiceMock
            ->method('check')
            ->willReturn(false);

        $container->set(CheckAddressInRestrictedAreaService::class, $checkAddressInRestrictedAreaServiceMock);

        /**
         * Create Country
         */
        $country = CountryFixture::getOne('Russia', 'RU');
        $container->get(CountryRepositoryInterface::class)->create($country);

        /**
         * Create Currency
         */
        $currencyRepository = $container->get(CurrencyRepositoryInterface::class);
        $currencyRepository->create(CurrencyFixture::getOne('RUB', 810, 'Russian ruble'));
        $currency = $currencyRepository->ofCode('RUB');

        /**
         * Create CargoType
         */
        $cargoTypeRepository = $container->get(CargoTypeRepositoryInterface::class);
        $cargoTypeRepository->create(CargoTypeFixture::getOne('test', 'test'));

        $shipments = $container->get(BulkCreateShipmentCommandHandler::class)(
            new BulkCreateShipmentCommand(BulkCreateShipmentDtoFixture::getOneFilled(
                to: $addressDto->address,
                recipient: ContactDtoFixture::getOne('test@gmail.com', 'recipient', ['+7777777777']),
                currencyCode: $currency->getCode(),
                products: [
                    ProductDtoFixture::getOneFilled(code: 'AA-1234', deliveryPeriod: 1),
                    ProductDtoFixture::getOneFilled(code: 'AA-4321', deliveryPeriod: 10),
                ]
            ))
        );

        $this->assertNotEmpty($shipments);
        $this->assertIsArray($shipments);
        $this->assertCount(2, $shipments); // count of complex shipments

        $shipment = reset($shipments);

        $this->assertNotNull($shipment);
        $this->assertInstanceOf(Shipment::class, $shipment);
    }

    public function testBulkCreateShipmentCommandHandlerTreeShipments()
    {
        $container = $this->getContainer();

        $addressDto = AddressDtoFixture::getOneFilled();
        $findExternalAddressMock = $this->createMock(FindExternalAddressInterface::class);
        $findExternalAddressMock
            ->method('find')
            ->willReturn($addressDto);

        $container->set(FindExternalAddressInterface::class, $findExternalAddressMock);

        $checkAddressInRestrictedAreaServiceMock = $this->createMock(CheckAddressInRestrictedAreaService::class);
        $checkAddressInRestrictedAreaServiceMock
            ->method('check')
            ->willReturn(false);

        $container->set(CheckAddressInRestrictedAreaService::class, $checkAddressInRestrictedAreaServiceMock);

        /**
         * Create Country
         */
        $country = CountryFixture::getOne('Russia', 'RU');
        $container->get(CountryRepositoryInterface::class)->create($country);

        /**
         * Create Currency
         */
        $currencyRepository = $container->get(CurrencyRepositoryInterface::class);
        $currencyRepository->create(CurrencyFixture::getOne('RUB', 810, 'Russian ruble'));
        $currency = $currencyRepository->ofCode('RUB');

        /**
         * Create CargoType
         */
        $cargoTypeRepository = $container->get(CargoTypeRepositoryInterface::class);
        $cargoTypeRepository->create(CargoTypeFixture::getOne('test', 'test'));

        $shipments = $container->get(BulkCreateShipmentCommandHandler::class)(
            new BulkCreateShipmentCommand(BulkCreateShipmentDtoFixture::getOneFilled(
                to: $addressDto->address,
                recipient: ContactDtoFixture::getOne('test@gmail.com', 'recipient', ['+7777777777']),
                currencyCode: $currency->getCode(),
                products: [
                    ProductDtoFixture::getOneFilled(code: 'AA-1234', deliveryPeriod: 5, store: StoreDtoFixture::getOneFilled(externalId: 5)),
                    ProductDtoFixture::getOneFilled(code: 'AA-4321', deliveryPeriod: 10, store: StoreDtoFixture::getOneFilled(externalId: 5)),
                    ProductDtoFixture::getOneFilled(code: 'AA-5678', deliveryPeriod: 10, store: StoreDtoFixture::getOneFilled(externalId: 10)),
                    ProductDtoFixture::getOneFilled(code: 'AA-8910', deliveryPeriod: 10, store: StoreDtoFixture::getOneFilled(externalId: 10)),
                ]
            ))
        );

        $this->assertNotEmpty($shipments);
        $this->assertIsArray($shipments);
        $this->assertCount(3, $shipments); // count of complex shipments

        $shipment = reset($shipments);

        $this->assertNotNull($shipment);
        $this->assertInstanceOf(Shipment::class, $shipment);
    }

    public function testBulkCreateShipmentCommandHandlerFourShipments()
    {
        $container = $this->getContainer();

        $addressDto = AddressDtoFixture::getOneFilled();
        $findExternalAddressMock = $this->createMock(FindExternalAddressInterface::class);
        $findExternalAddressMock
            ->method('find')
            ->willReturn($addressDto);

        $container->set(FindExternalAddressInterface::class, $findExternalAddressMock);

        $checkAddressInRestrictedAreaServiceMock = $this->createMock(CheckAddressInRestrictedAreaService::class);
        $checkAddressInRestrictedAreaServiceMock
            ->method('check')
            ->willReturn(false);

        $container->set(CheckAddressInRestrictedAreaService::class, $checkAddressInRestrictedAreaServiceMock);

        /**
         * Create Country
         */
        $country = CountryFixture::getOne('Russia', 'RU');
        $container->get(CountryRepositoryInterface::class)->create($country);

        /**
         * Create Currency
         */
        $currencyRepository = $container->get(CurrencyRepositoryInterface::class);
        $currencyRepository->create(CurrencyFixture::getOne('RUB', 810, 'Russian ruble'));
        $currency = $currencyRepository->ofCode('RUB');

        /**
         * Create CargoType
         */
        $cargoTypeRepository = $container->get(CargoTypeRepositoryInterface::class);
        $cargoTypeRepository->create(CargoTypeFixture::getOne('test', 'test'));

        $shipments = $container->get(BulkCreateShipmentCommandHandler::class)(
            new BulkCreateShipmentCommand(BulkCreateShipmentDtoFixture::getOneFilled(
                to: $addressDto->address,
                recipient: ContactDtoFixture::getOne('test@gmail.com', 'recipient', ['+7777777777']),
                currencyCode: $currency->getCode(),
                products: [
                    ProductDtoFixture::getOneFilled(deliveryPeriod: 5, store: StoreDtoFixture::getOneFilled(externalId: 5)),
                    ProductDtoFixture::getOneFilled(code: 'AA-321', deliveryPeriod: 10, store: StoreDtoFixture::getOneFilled(externalId: 5)),
                    ProductDtoFixture::getOneFilled(code: 'AA-4321', deliveryPeriod: 5, store: StoreDtoFixture::getOneFilled(externalId: 10)),
                    ProductDtoFixture::getOneFilled(code: 'AA-6542', deliveryPeriod: 10, store: StoreDtoFixture::getOneFilled(externalId: 10)),
                ]
            ))
        );

        $this->assertNotEmpty($shipments);
        $this->assertIsArray($shipments);
        $this->assertCount(4, $shipments); // count of complex shipments

        $shipment = reset($shipments);

        $this->assertNotNull($shipment);
        $this->assertInstanceOf(Shipment::class, $shipment);
    }

    public function testBulkCreateShipmentCommandHandlerFourShipmentsWithRestrictPackage(): void
    {
        $container = $this->getContainer();

        $addressDto = AddressDtoFixture::getOneFilled();
        $findExternalAddressMock = $this->createMock(FindExternalAddressInterface::class);
        $findExternalAddressMock
            ->method('find')
            ->willReturn($addressDto);

        $container->set(FindExternalAddressInterface::class, $findExternalAddressMock);

        $checkAddressInRestrictedAreaServiceMock = $this->createMock(CheckAddressInRestrictedAreaService::class);
        $checkAddressInRestrictedAreaServiceMock
            ->method('check')
            ->willReturn(false);

        $container->set(CheckAddressInRestrictedAreaService::class, $checkAddressInRestrictedAreaServiceMock);

        /**
         * Create Country
         */
        $country = CountryFixture::getOne('Russia', 'RU');
        $container->get(CountryRepositoryInterface::class)->create($country);

        /**
         * Create TariffPlan and DeliveryService
         */
        $deliveryRepository = $container->get(DeliveryServiceRepositoryInterface::class);
        $deliveryRepository->create(
            DeliveryServiceFixture::getOne('dellin', 'Деловые линии')
        );

        $deliveryMethodRepository = $container->get(DeliveryMethodRepositoryInterface::class);
        $deliveryMethodRepository->create(
            DeliveryMethodFixture::getOne('test', 'test')
        );
        $deliveryMethod = $deliveryMethodRepository->ofCode('test');
        $deliveryService = $deliveryRepository->ofCode('dellin');

        $tariffPlanRepository = $container->get(TariffPlanRepositoryInterface::class);
        $tariffPlanRepository->create(TariffPlanFixture::getOne($deliveryService, $deliveryMethod, 'express', 'Экспресс'));

        /**
         * Create Currency
         */
        $currencyRepository = $container->get(CurrencyRepositoryInterface::class);
        $currencyRepository->create(CurrencyFixture::getOne('RUB', 810, 'Russian ruble'));
        $currency = $currencyRepository->ofCode('RUB');

        /**
         * Create CargoType
         */
        $cargoTypeRepository = $container->get(CargoTypeRepositoryInterface::class);
        $cargoTypeRepository->create(CargoTypeFixture::getOne('test', 'test'));

        /**
         * Create package restrict
         */
        $deliveryServiceRestrictPackageRepository = $container->get(DeliveryServiceRestrictPackageRepositoryInterface::class);
        $deliveryService = $deliveryRepository->ofCode('dellin');
        $deliveryServiceRestrictPackage = DeliverServiceRestrictPackageFixture::getOne(
            $deliveryService,
            100,
            200,
            300,
            400
        );
        $deliveryServiceRestrictPackageRepository->create($deliveryServiceRestrictPackage);

        $shipments = $container->get(BulkCreateShipmentCommandHandler::class)(
            new BulkCreateShipmentCommand(BulkCreateShipmentDtoFixture::getOneFilled(
                to: $addressDto->address,
                recipient: ContactDtoFixture::getOne('test@gmail.com', 'recipient', ['+7777777777']),
                currencyCode: $currency->getCode(),
                products: [
                    ProductDtoFixture::getOneFilled(
                        code: 'AA-5678',
                        weight: 10,
                        width: 20,
                        height: 30,
                        length: 40,
                        deliveryPeriod: 10,
                        store: StoreDtoFixture::getOneFilled(externalId: 10)
                    ),
                    ProductDtoFixture::getOneFilled(
                        code: 'AA-8910',
                        weight: 10,
                        width: 20,
                        height: 30,
                        length: 40,
                        deliveryPeriod: 10,
                        store: StoreDtoFixture::getOneFilled(externalId: 10)
                    ),
                ]
            ))
        );

        $this->assertNotEmpty($shipments);
        $this->assertIsArray($shipments);
        $this->assertCount(1, $shipments); // count of complex shipments

        $shipment = reset($shipments);

        $this->assertNotNull($shipment);
        $this->assertInstanceOf(Shipment::class, $shipment);
        $this->assertCount(1, $shipment->getPackages());
    }

    public function testBulkCreateShipmentCommandHandlerWithCargoRestrictions()
    {
        $container = $this->getContainer();

        $addressDto = AddressDtoFixture::getOneFilled();
        $findExternalAddressMock = $this->createMock(FindExternalAddressInterface::class);
        $findExternalAddressMock
            ->method('find')
            ->willReturn($addressDto);

        $container->set(FindExternalAddressInterface::class, $findExternalAddressMock);

        $checkAddressInRestrictedAreaServiceMock = $this->createMock(CheckAddressInRestrictedAreaService::class);
        $checkAddressInRestrictedAreaServiceMock
            ->method('check')
            ->willReturn(false);

        $container->set(CheckAddressInRestrictedAreaService::class, $checkAddressInRestrictedAreaServiceMock);

        /**
         * Create Country
         */
        $country = CountryFixture::getOne('Russia', 'RU');
        $container->get(CountryRepositoryInterface::class)->create($country);

        /**
         * Create TariffPlan and DeliveryService
         */
        $deliveryRepository = $container->get(DeliveryServiceRepositoryInterface::class);
        $deliveryRepository->create(
            DeliveryServiceFixture::getOne('dellin', 'Деловые линии')
        );

        $deliveryMethodRepository = $container->get(DeliveryMethodRepositoryInterface::class);
        $deliveryMethodRepository->create(
            DeliveryMethodFixture::getOne('test', 'test')
        );
        $deliveryMethod = $deliveryMethodRepository->ofCode('test');
        $deliveryService = $deliveryRepository->ofCode('dellin');

        $tariffPlanRepository = $container->get(TariffPlanRepositoryInterface::class);
        $tariffPlanRepository->create(TariffPlanFixture::getOne($deliveryService, $deliveryMethod, 'express', 'Экспресс'));

        /**
         * Create Currency
         */
        $currencyRepository = $container->get(CurrencyRepositoryInterface::class);
        $currencyRepository->create(CurrencyFixture::getOne('RUB', 810, 'Russian ruble'));
        $currency = $currencyRepository->ofCode('RUB');

        /**
         * Create CargoType
         */
        $cargoTypeRepository = $container->get(CargoTypeRepositoryInterface::class);
        $cargoTypeRepository->create(CargoTypeFixture::getOne('test', 'test'));

        $shipments = $container->get(BulkCreateShipmentCommandHandler::class)(
            new BulkCreateShipmentCommand(BulkCreateShipmentDtoFixture::getOneFilled(
                to: $addressDto->address,
                recipient: ContactDtoFixture::getOne('test@gmail.com', 'recipient', ['+7777777777']),
                currencyCode: $currency->getCode(),
                products: [
                    ProductDtoFixture::getOneFilled(code: 'AA-1234', deliveryPeriod: 1),
                    ProductDtoFixture::getOneFilled(code: 'AA-4321', deliveryPeriod: 10),
                ],
                cargoRestrictions: [
                    CargoRestrictionDtoFixture::getOne('test', 100, 200, 300, 400, 500, 600),
                ]
            ))
        );

        $this->assertNotEmpty($shipments);
        $this->assertIsArray($shipments);
        $this->assertCount(2, $shipments); // count of complex shipments

        $shipment = reset($shipments);

        $this->assertNotNull($shipment);
        $this->assertInstanceOf(Shipment::class, $shipment);

        $cargoRestrictionRepository = $container->get(CargoRestrictionRepositoryInterface::class);
        $cargoRestrictions = $cargoRestrictionRepository->ofShipmentId($shipment->getId());

        $this->assertCount(1, $cargoRestrictions);
    }
}
