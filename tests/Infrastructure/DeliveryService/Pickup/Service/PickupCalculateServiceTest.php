<?php

namespace App\Tests\Infrastructure\DeliveryService\Pickup\Service;

use App\Domain\Shipment\Dto\CalculateDto;
use App\Infrastructure\DeliveryService\Pickup\Service\PickupCalculateService;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\Shipment\PackageFixture;
use App\Tests\Fixture\Shipment\PackageProductFixture;
use App\Tests\Fixture\Shipment\ProductFixture;
use App\Tests\Fixture\Shipment\ShipmentFixture;
use App\Tests\Fixture\Shipment\StoreFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PickupCalculateServiceTest extends KernelTestCase
{
    public function testPickupCalculatePriceAndPeriod()
    {
        $container = $this->getContainer();
        $service = $container->get(PickupCalculateService::class);

        $shipment = ShipmentFixture::getOneFilled(
            from: AddressFixture::getOneFilled(
                address: 'г Москва, р-н Вешняки, ул Юности, д 5',
                inputData: [
                    'region_fias_id' => '0c5b2444-70a0-4932-980c-b4dc0d3f02b5',
                ]
            ),
            to: AddressFixture::getOneFilled(
                address: 'г Москва, р-н Вешняки, ул Юности, д 6',
                inputData: [
                    'region_fias_id' => '0c5b2444-70a0-4932-980c-b4dc0d3f02b5',
                ]
            ),
            packages: [
                PackageFixture::getOneFilled(
                    100,
                    100,
                    100,
                    100,
                    5,
                    products: [
                        PackageProductFixture::getOne(
                            1,
                            ProductFixture::getOneFilled('AA-4321', store: StoreFixture::getOneFilled(isPickup: true))
                        ),
                        PackageProductFixture::getOne(
                            1,
                            ProductFixture::getOneFilled('AA-1234', store: StoreFixture::getOneFilled(isPickup: true))
                        ),
                    ]
                ),
                PackageFixture::getOneFilled(
                    200,
                    200,
                    200,
                    200,
                    10,
                    products: [
                        PackageProductFixture::getOne(
                            1,
                            ProductFixture::getOneFilled('AA-6543', store: StoreFixture::getOneFilled(isPickup: true)),
                        ),
                        PackageProductFixture::getOne(
                            1,
                            ProductFixture::getOneFilled('AA-8765', store: StoreFixture::getOneFilled(isPickup: true)),
                        ),
                    ]
                ),
            ],
            psd: new \DateTime('+4 days')
        );

        $calculateDto = $service->calculate($shipment);

        $this->assertNotNull($calculateDto);
        $this->assertInstanceOf(CalculateDto::class, $calculateDto);
        $this->assertEquals(4, $calculateDto->minPeriod);
        $this->assertEquals(5, $calculateDto->maxPeriod);
        $this->assertEquals(0, $calculateDto->deliveryCost);
        $this->assertEquals(0, $calculateDto->deliveryTotalCost);
        $this->assertEquals(0, $calculateDto->deliveryTotalCostTax);
    }

    public function testPickupCalculatePriceAndPeriodIfStoreIsPickupFalse()
    {
        $container = $this->getContainer();
        $service = $container->get(PickupCalculateService::class);

        $shipment = ShipmentFixture::getOneFilled(
            from: AddressFixture::getOneFilled(
                address: 'г Москва, р-н Вешняки, ул Юности, д 5',
                inputData: [
                    'region_fias_id' => '0c5b2444-70a0-4932-980c-b4dc0d3f02b5',
                ]
            ),
            to: AddressFixture::getOneFilled(
                address: 'г Москва, р-н Вешняки, ул Юности, д 6',
                inputData: [
                    'region_fias_id' => '0c5b2444-70a0-4932-980c-b4dc0d3f02b5',
                ]
            ),
            packages: [
                PackageFixture::getOneFilled(
                    100,
                    100,
                    100,
                    100,
                    5,
                    products: [
                        PackageProductFixture::getOne(
                            1,
                            ProductFixture::getOneFilled('AA-4321', store: StoreFixture::getOneFilled(isPickup: false)),
                        ),
                        PackageProductFixture::getOne(
                            1,
                            ProductFixture::getOneFilled('AA-1234', store: StoreFixture::getOneFilled(isPickup: false)),
                        ),
                    ]
                ),
            ],
            psd: new \DateTime('+4 days')
        );

        $calculateDto = $service->calculate($shipment);

        $this->assertNull($calculateDto);
    }
}
