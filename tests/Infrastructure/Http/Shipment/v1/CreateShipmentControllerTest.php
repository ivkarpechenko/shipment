<?php

namespace App\Tests\Infrastructure\Http\Shipment\v1;

use App\Application\Address\Query\External\FindExternalAddressInterface;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use App\Domain\Shipment\Service\CheckAddressInRestrictedAreaService;
use App\Tests\Fixture\Address\AddressDtoFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\HttpTestCase;
use Symfony\Component\Uid\Uuid;

class CreateShipmentControllerTest extends HttpTestCase
{
    public function testCreateShipment()
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

        $this->client->request('POST', 'api/v1/shipment', [
            'from' => 'address',
            'to' => 'address',
            'sender' => [
                'email' => 'sender@gmail.com',
                'name' => 'sender',
                'phones' => [
                    '+77777777777',
                ],
            ],
            'recipient' => [
                'email' => 'recipient@gmail.com',
                'name' => 'recipient',
                'phones' => [
                    '+8888888888',
                ],
            ],
            'currencyCode' => $currency->getCode(),
            'packages' => [
                [
                    'price' => 1,
                    'width' => 1,
                    'height' => 1,
                    'length' => 1,
                    'weight' => 1,
                    'products' => [
                        [
                            'code' => 'test product',
                            'description' => 'test product description',
                            'price' => 123.0,
                            'quantity' => 2,
                        ],
                    ],
                ],
            ],
            'psd' => (new \DateTime('now'))->format('Y-m-d'),
            'psdStartTime' => '00:00:00',
            'psdEndTime' => '23:59:59',
        ], server: ['CONTENT_TYPE' => 'application/json']);

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(201);

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);
        $this->assertStringContainsString('id', $response);

        $responseArray = json_decode($response, true);

        $this->assertNotEmpty($responseArray);
        $this->assertIsArray($responseArray);
        $this->assertArrayHasKey('id', $responseArray);
        $this->assertInstanceOf(Uuid::class, Uuid::fromString($responseArray['id']));
    }
}
