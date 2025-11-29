<?php

namespace App\Tests\Application\Shipment\Command;

use App\Application\Address\Query\External\FindExternalAddressInterface;
use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Shipment\Command\TekTorgCreateShipmentCommand;
use App\Application\Shipment\Command\TekTorgCreateShipmentCommandHandler;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\DeliveryService\Repository\DeliveryServiceRestrictPackageRepositoryInterface;
use App\Domain\Directory\Entity\OkatoOktmo;
use App\Domain\Directory\Service\FindOkatoOktmoService;
use App\Domain\Shipment\Entity\Shipment;
use App\Domain\Shipment\Repository\CargoRestrictionRepositoryInterface;
use App\Domain\Shipment\Repository\CargoTypeRepositoryInterface;
use App\Domain\Shipment\Service\CheckAddressInRestrictedAreaService;
use App\Domain\TariffPlan\Repository\TariffPlanRepositoryInterface;
use App\Infrastructure\DaData\Service\FindAddressByOktmoService;
use App\Tests\Fixture\Address\AddressDtoFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\Fixture\DaData\DaDataOktmoDtoFixture;
use App\Tests\Fixture\DeliveryMethod\DeliveryMethodFixture;
use App\Tests\Fixture\DeliveryService\DeliverServiceRestrictPackageFixture;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\Fixture\Shipment\BulkCreateShipmentTekTorgDtoFixture;
use App\Tests\Fixture\Shipment\CargoRestrictionDtoFixture;
use App\Tests\Fixture\Shipment\CargoTypeFixture;
use App\Tests\Fixture\Shipment\ContactDtoFixture;
use App\Tests\Fixture\Shipment\ProductDtoFixture;
use App\Tests\Fixture\Shipment\StoreDtoFixture;
use App\Tests\Fixture\TariffPlan\TariffPlanFixture;
use App\Tests\MessageBusTestCase;

class TekTorgCreateShipmentCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new TekTorgCreateShipmentCommand(BulkCreateShipmentTekTorgDtoFixture::getOneFilled())
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(TekTorgCreateShipmentCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new TekTorgCreateShipmentCommand(BulkCreateShipmentTekTorgDtoFixture::getOneFilled());
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testTekTorgCreateShipmentCommandHandler()
    {
        $okato = '46458000000';
        $container = $this->getContainer();

        $daDataOktmoDto = DaDataOktmoDtoFixture::getOne();
        $findExternalAddressMock = $this->createMock(FindAddressByOktmoService::class);
        $findExternalAddressMock
            ->method('find')
            ->willReturn($daDataOktmoDto);
        $container->set(FindAddressByOktmoService::class, $findExternalAddressMock);

        $okatoServiceMock = $this->createMock(FindOkatoOktmoService::class);
        $mockOkatoOktmo = new OkatoOktmo('46458000000', '4645800', 'Test Location');
        $okatoServiceMock->method('ofOkato')
            ->with('46458000000')
            ->willReturn($mockOkatoOktmo);
        $container->set(FindOkatoOktmoService::class, $okatoServiceMock);

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

        $shipments = $container->get(TekTorgCreateShipmentCommandHandler::class)(
            new TekTorgCreateShipmentCommand(BulkCreateShipmentTekTorgDtoFixture::getOneFilled(
                okato: $okato,
                oktmo: null,
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

    public function testOktmoTekTorgCreateShipmentCommandHandler()
    {
        $oktmo = '4645800';
        $container = $this->getContainer();

        $daDataOktmoDto = DaDataOktmoDtoFixture::getOne();
        $findExternalAddressMock = $this->createMock(FindAddressByOktmoService::class);
        $findExternalAddressMock
            ->method('find')
            ->willReturn($daDataOktmoDto);
        $container->set(FindAddressByOktmoService::class, $findExternalAddressMock);

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

        $shipments = $container->get(TekTorgCreateShipmentCommandHandler::class)(
            new TekTorgCreateShipmentCommand(BulkCreateShipmentTekTorgDtoFixture::getOneFilled(
                okato: null,
                oktmo: $oktmo,
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

    public function testTekTorgCreateShipmentCommandHandlerThreeShipments()
    {
        $okato = '46458000000';
        $container = $this->getContainer();

        $daDataOktmoDto = DaDataOktmoDtoFixture::getOne();
        $findExternalAddressMock = $this->createMock(FindAddressByOktmoService::class);
        $findExternalAddressMock
            ->method('find')
            ->willReturn($daDataOktmoDto);
        $container->set(FindAddressByOktmoService::class, $findExternalAddressMock);

        $okatoServiceMock = $this->createMock(FindOkatoOktmoService::class);
        $mockOkatoOktmo = new OkatoOktmo('46458000000', '4645800', 'Test Location');
        $okatoServiceMock->method('ofOkato')
            ->with('46458000000')
            ->willReturn($mockOkatoOktmo);
        $container->set(FindOkatoOktmoService::class, $okatoServiceMock);

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

        $shipments = $container->get(TekTorgCreateShipmentCommandHandler::class)(
            new TekTorgCreateShipmentCommand(BulkCreateShipmentTekTorgDtoFixture::getOneFilled(
                okato: $okato,
                oktmo: null,
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

    public function testTekTorgCreateShipmentCommandHandlerFourShipments()
    {
        $okato = '46458000000';
        $container = $this->getContainer();

        $daDataOktmoDto = DaDataOktmoDtoFixture::getOne();
        $findExternalAddressMock = $this->createMock(FindAddressByOktmoService::class);
        $findExternalAddressMock
            ->method('find')
            ->willReturn($daDataOktmoDto);
        $container->set(FindAddressByOktmoService::class, $findExternalAddressMock);

        $okatoServiceMock = $this->createMock(FindOkatoOktmoService::class);
        $mockOkatoOktmo = new OkatoOktmo('46458000000', '4645800', 'Test Location');
        $okatoServiceMock->method('ofOkato')
            ->with('46458000000')
            ->willReturn($mockOkatoOktmo);
        $container->set(FindOkatoOktmoService::class, $okatoServiceMock);

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

        $shipments = $container->get(TekTorgCreateShipmentCommandHandler::class)(
            new TekTorgCreateShipmentCommand(BulkCreateShipmentTekTorgDtoFixture::getOneFilled(
                okato: $okato,
                oktmo: null,
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

    public function testTekTorgCreateShipmentCommandHandlerFourShipmentsWithRestrictPackage(): void
    {
        $okato = '46458000000';
        $container = $this->getContainer();

        $daDataOktmoDto = DaDataOktmoDtoFixture::getOne();
        $findExternalAddressMock = $this->createMock(FindAddressByOktmoService::class);
        $findExternalAddressMock
            ->method('find')
            ->willReturn($daDataOktmoDto);
        $container->set(FindAddressByOktmoService::class, $findExternalAddressMock);

        $okatoServiceMock = $this->createMock(FindOkatoOktmoService::class);
        $mockOkatoOktmo = new OkatoOktmo('46458000000', '4645800', 'Test Location');
        $okatoServiceMock->method('ofOkato')
            ->with('46458000000')
            ->willReturn($mockOkatoOktmo);
        $container->set(FindOkatoOktmoService::class, $okatoServiceMock);

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

        $shipments = $container->get(TekTorgCreateShipmentCommandHandler::class)(
            new TekTorgCreateShipmentCommand(BulkCreateShipmentTekTorgDtoFixture::getOneFilled(
                okato: $okato,
                oktmo: null,
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

    public function testTekTorgCreateShipmentCommandHandlerWithCargoRestrictions()
    {
        $okato = '46458000000';
        $container = $this->getContainer();

        $daDataOktmoDto = DaDataOktmoDtoFixture::getOne();
        $findExternalAddressMock = $this->createMock(FindAddressByOktmoService::class);
        $findExternalAddressMock
            ->method('find')
            ->willReturn($daDataOktmoDto);
        $container->set(FindAddressByOktmoService::class, $findExternalAddressMock);

        $okatoServiceMock = $this->createMock(FindOkatoOktmoService::class);
        $mockOkatoOktmo = new OkatoOktmo('46458000000', '4645800', 'Test Location');
        $okatoServiceMock->method('ofOkato')
            ->with('46458000000')
            ->willReturn($mockOkatoOktmo);
        $container->set(FindOkatoOktmoService::class, $okatoServiceMock);

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

        $shipments = $container->get(TekTorgCreateShipmentCommandHandler::class)(
            new TekTorgCreateShipmentCommand(BulkCreateShipmentTekTorgDtoFixture::getOneFilled(
                okato: $okato,
                oktmo: null,
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
