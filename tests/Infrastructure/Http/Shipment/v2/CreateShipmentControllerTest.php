<?php

namespace App\Tests\Infrastructure\Http\Shipment\v2;

use App\Application\Address\Query\External\FindExternalAddressInterface;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Domain\Shipment\Repository\CargoTypeRepositoryInterface;
use App\Domain\Shipment\Service\CheckAddressInRestrictedAreaService;
use App\Tests\Fixture\Address\AddressDtoFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\Fixture\Shipment\CargoTypeFixture;
use App\Tests\HttpTestCase;

class CreateShipmentControllerTest extends HttpTestCase
{
    public function testCreateShipmentV2()
    {
        $container = $this->getContainer();

        /**
         * Create Country
         */
        $countryRepository = $container->get(CountryRepositoryInterface::class);
        $countryRepository->create(CountryFixture::getOne('Russia', 'RU'));
        $country = $countryRepository->ofCode('RU');

        /**
         * Mock find external address
         */
        $findExternalAddressMock = $this->createMock(FindExternalAddressInterface::class);
        $findExternalAddressMock
            ->method('find')
            ->willReturn(
                AddressDtoFixture::getOneFilled(
                    'address',
                    country: $country->getName(),
                    countryIsoCode: $country->getCode()
                )
            );

        $container->set(FindExternalAddressInterface::class, $findExternalAddressMock);

        /**
         * Mock check address in restricted area
         */
        $checkAddressInRestrictedAreaServiceMock = $this->createMock(CheckAddressInRestrictedAreaService::class);
        $checkAddressInRestrictedAreaServiceMock
            ->method('check')
            ->willReturn(false);

        $container->set(CheckAddressInRestrictedAreaService::class, $checkAddressInRestrictedAreaServiceMock);

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

        $this->client->request('POST', 'api/v2/shipment', [
            'to' => 'address',
            'recipient' => [
                'email' => 'recipient@gmail.com',
                'name' => 'recipient',
                'phones' => [
                    '+8888888888',
                ],
            ],
            'currencyCode' => $currency->getCode(),
            'products' => [
                [
                    'code' => 'AA-1234',
                    'description' => 'desc',
                    'price' => 100.0,
                    'weight' => 10,
                    'width' => 10,
                    'height' => 10,
                    'length' => 10,
                    'quantity' => 1,
                    'isFragile' => false,
                    'isFlammable' => false,
                    'isCanRotate' => false,
                    'deliveryPeriod' => 5,
                    'store' => [
                        'contact' => [
                            'email' => 'test@gmail.com',
                            'name' => 'tester',
                            'phones' => [
                                '+8888888888',
                            ],
                        ],
                        'externalId' => 1000,
                        'maxWeight' => 1000,
                        'maxVolume' => 1000,
                        'maxLength' => 1000,
                        'isPickup' => true,
                        'address' => 'address',
                        'schedules' => [
                            [
                                'day' => 1,
                                'startTime' => '10:00:00',
                                'endTime' => '19:00:00',
                            ],
                        ],
                        'psd' => (new \DateTime('now'))->format('Y-m-d'),
                        'psdStartTime' => '10:00:00',
                        'psdEndTime' => '19:00:00',
                    ],
                ],
            ],
            'cargoRestrictions' => [
                [
                    'code' => 'test',
                    'maxWidth' => 100,
                    'maxHeight' => 200,
                    'maxLength' => 300,
                    'maxWeight' => 400,
                    'maxVolume' => 500,
                    'maxSumDimensions' => 600,
                ],
            ],
        ], server: ['CONTENT_TYPE' => 'application/json']);

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(201);

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);
        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('from', $response);
        $this->assertStringContainsString('to', $response);
        $this->assertStringContainsString('sender', $response);
        $this->assertStringContainsString('recipient', $response);
        $this->assertStringContainsString('currency', $response);
        $this->assertStringContainsString('packages', $response);
        $this->assertStringContainsString('products', $response);
        $this->assertStringContainsString('code', $response);
        $this->assertStringContainsString('deliveryPeriod', $response);
        $this->assertStringContainsString('store', $response);
        $this->assertStringContainsString('psd', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);

        $responseArray = json_decode($response, true);

        $this->assertNotEmpty($responseArray);
        $this->assertIsArray($responseArray);

        $this->assertCount(1, $responseArray);
    }
}
