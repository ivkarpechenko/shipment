<?php

namespace App\Tests\Infrastructure\Http\Shipment\v1;

use App\Domain\Address\Repository\AddressRepositoryInterface;
use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Domain\Shipment\Repository\ShipmentRepositoryInterface;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Contact\ContactFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\Fixture\Shipment\ShipmentFixture;
use App\Tests\HttpTestCase;
use Symfony\Component\Uid\Uuid;

class UpdateShipmentControllerTest extends HttpTestCase
{
    public function testUpdateShipmentPsd()
    {
        $shipmentId = $this->createShipment();

        $this->assertNotNull($shipmentId);
        $this->assertInstanceOf(Uuid::class, $shipmentId);

        $this->client->request('PUT', "api/v1/shipment/{$shipmentId->toRfc4122()}", [
            'psd' => '2023-01-01',
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(204);
    }

    public function testUpdateShipmentPsdStartTime()
    {
        $shipmentId = $this->createShipment();

        $this->assertNotNull($shipmentId);
        $this->assertInstanceOf(Uuid::class, $shipmentId);

        $this->client->request('PUT', "api/v1/shipment/{$shipmentId->toRfc4122()}", [
            'psdStartTime' => '00:00:00',
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(204);
    }

    public function testUpdateShipmentPsdEndTime()
    {
        $shipmentId = $this->createShipment();

        $this->assertNotNull($shipmentId);
        $this->assertInstanceOf(Uuid::class, $shipmentId);

        $this->client->request('PUT', "api/v1/shipment/{$shipmentId->toRfc4122()}", [
            'psdEndTime' => '23:59:59',
        ]);

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(204);
    }

    protected function createShipment(): Uuid
    {
        $container = $this->getContainer();

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

        return $container->get(ShipmentRepositoryInterface::class)->create($newShipment);
    }
}
