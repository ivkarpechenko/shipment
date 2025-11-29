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

class FindShipmentControllerTest extends HttpTestCase
{
    public function testFindById()
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

        $shipmentId = $container->get(ShipmentRepositoryInterface::class)->create($newShipment);

        $this->assertNotNull($shipmentId);
        $this->assertInstanceOf(Uuid::class, $shipmentId);

        $this->client->request('GET', "api/v1/shipment/find-by-id/{$shipmentId->toRfc4122()}");

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(200);

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);
        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('from', $response);
        $this->assertStringContainsString('to', $response);
        $this->assertStringContainsString('sender', $response);
        $this->assertStringContainsString('recipient', $response);
        $this->assertStringContainsString('currency', $response);
        $this->assertStringContainsString('packages', $response);
        $this->assertStringContainsString('psd', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);
    }
}
