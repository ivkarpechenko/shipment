<?php

namespace App\Tests\Infrastructure\Http\Shipment\v1;

use App\Domain\Address\Repository\AddressRepositoryInterface;
use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Domain\Shipment\Repository\CalculateRepositoryInterface;
use App\Domain\Shipment\Repository\ShipmentRepositoryInterface;
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
use App\Tests\Fixture\Shipment\CalculateFixture;
use App\Tests\Fixture\Shipment\ShipmentFixture;
use App\Tests\Fixture\TariffPlan\TariffPlanFixture;
use App\Tests\HttpTestCase;

class FindCalculateControllerTest extends HttpTestCase
{
    public function testFindById()
    {
        $container = $this->getContainer();

        $shipment = $this->createShipment($container);
        $tariffPlan = $this->createTariffPlan($container);
        $shipment = $container->get(ShipmentRepositoryInterface::class)->ofId($shipment->getId());
        $tariffPlan = $container->get(TariffPlanRepositoryInterface::class)->ofId($tariffPlan->getId());
        $calculate = CalculateFixture::getOneFilled(
            $shipment,
            $tariffPlan,
            1,
            1,
            100.0,
            120.0
        );

        $container->get(CalculateRepositoryInterface::class)->create($calculate);

        $this->client->request(
            'GET',
            "api/v1/shipment/calculate/find-by-id/{$calculate->getId()->toRfc4122()}"
        );

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(200);

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);
        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('shipment', $response);
        $this->assertStringContainsString('minPeriod', $response);
        $this->assertStringContainsString('maxPeriod', $response);
        $this->assertStringContainsString('deliveryCost', $response);
        $this->assertStringContainsString('deliveryTotalCost', $response);
        $this->assertStringContainsString('deliveryTotalCostTax', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('expiredAt', $response);
    }

    public function testFindByShipmentId()
    {
        $container = $this->getContainer();

        $shipment = $this->createShipment($container);
        $tariffPlan = $this->createTariffPlan($container);
        $shipment = $container->get(ShipmentRepositoryInterface::class)->ofId($shipment->getId());
        $tariffPlan = $container->get(TariffPlanRepositoryInterface::class)->ofId($tariffPlan->getId());

        $calculate = CalculateFixture::getOneFilled(
            $shipment,
            $tariffPlan,
            1,
            1,
            100.0,
            120.0
        );

        $container->get(CalculateRepositoryInterface::class)->create($calculate);

        $this->client->request(
            'GET',
            "api/v1/shipment/calculate/find-by-shipment-id/{$shipment->getId()->toRfc4122()}"
        );

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(200);

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);
        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('shipment', $response);
        $this->assertStringContainsString('minPeriod', $response);
        $this->assertStringContainsString('maxPeriod', $response);
        $this->assertStringContainsString('deliveryCost', $response);
        $this->assertStringContainsString('deliveryTotalCost', $response);
        $this->assertStringContainsString('deliveryTotalCostTax', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('expiredAt', $response);
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
