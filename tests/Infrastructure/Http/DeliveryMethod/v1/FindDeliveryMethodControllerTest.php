<?php

namespace App\Tests\Infrastructure\Http\DeliveryMethod\v1;

use App\Application\Address\Query\External\FindExternalAddressInterface;
use App\Application\Shipment\Command\BulkCreateShipmentCommand;
use App\Application\Shipment\Command\BulkCreateShipmentCommandHandler;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\Shipment\Repository\CargoTypeRepositoryInterface;
use App\Domain\Shipment\Service\CheckAddressInRestrictedAreaService;
use App\Infrastructure\DBAL\Repository\Doctrine\DeliveryMethod\DoctrineDeliveryMethodRepository;
use App\Tests\Fixture\Address\AddressDtoFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\Fixture\DeliveryMethod\DeliveryMethodFixture;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\Fixture\Shipment\BulkCreateShipmentDtoFixture;
use App\Tests\Fixture\Shipment\CargoTypeFixture;
use App\Tests\Fixture\Shipment\ContactDtoFixture;
use App\Tests\Fixture\Shipment\ProductDtoFixture;
use App\Tests\HttpTestCase;

class FindDeliveryMethodControllerTest extends HttpTestCase
{
    public function testFindByShipmentId()
    {
        $repositoryDeliveryService = $this->getContainer()->get(DeliveryServiceRepositoryInterface::class);
        $repositoryDeliveryMethod = $this->getContainer()->get(DoctrineDeliveryMethodRepository::class);

        $repositoryDeliveryService->create(DeliveryServiceFixture::getOne('cdek', 'cdek'));
        $repositoryDeliveryMethod->create(DeliveryMethodFixture::getOne('pvz', 'PVZ'));
        $repositoryDeliveryMethod->create(DeliveryMethodFixture::getOne('courier', 'courier'));
        $repositoryDeliveryMethod->create(DeliveryMethodFixture::getOne('pickup', 'pickup'));

        /** @var DeliveryService $deliveryService */
        $deliveryService = $repositoryDeliveryService->ofCode('cdek');

        $deliveryMethodPvz = $repositoryDeliveryMethod->ofCode('pvz');
        $deliveryMethodCourier = $repositoryDeliveryMethod->ofCode('courier');
        $deliveryMethodPickup = $repositoryDeliveryMethod->ofCode('pickup');

        $deliveryService->addDeliveryMethod($deliveryMethodPvz);
        $deliveryService->addDeliveryMethod($deliveryMethodCourier);
        $deliveryService->addDeliveryMethod($deliveryMethodPickup);
        $repositoryDeliveryService->update($deliveryService);

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

        $this->client->request(
            'GET',
            "/api/v1/delivery-method/find-by-shipment-id/{$shipments[0]->getId()->toRfc4122()}"
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('code', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('deliveryServices', $response);

        $this->assertStringContainsString('cdek', $response);
        $this->assertStringContainsString('PVZ', $response);

        $responseArray = json_decode($response, true);

        $this->assertNotEmpty($responseArray);
        $this->assertIsArray($responseArray);

        $this->assertCount(2, $responseArray);
    }
}
