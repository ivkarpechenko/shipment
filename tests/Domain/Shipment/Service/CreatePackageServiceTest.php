<?php

namespace App\Tests\Domain\Shipment\Service;

use App\Domain\Shipment\Entity\Package;
use App\Domain\Shipment\Service\CreatePackageService;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Contact\ContactFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\DaData\DaDataAddressDtoFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\Fixture\Shipment\ProductFixture;
use App\Tests\Fixture\Shipment\StoreFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreatePackageServiceTest extends KernelTestCase
{
    public function testCreatePackageIfDifferentDeliveryPeriod()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');
        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $city = CityFixture::getOne($region, 'city', 'Moskva');

        $address = AddressFixture::getOneFromAddressDto($city, DaDataAddressDtoFixture::getOne());
        $contact = ContactFixture::getOne('test@gmail.com', 'test', ['+777777777']);

        $store = StoreFixture::getOneFilled(
            $contact,
            $address,
            1,
            1000,
            1000,
            1000,
            false,
            [
                ProductFixture::getOne(
                    'AA-1234',
                    'desc',
                    '1000.0',
                    30000,
                    360,
                    870,
                    150,
                    1,
                    false,
                    false,
                    false,
                    1
                ),
                ProductFixture::getOne(
                    'AA-1234',
                    'desc',
                    '1000.0',
                    30000,
                    360,
                    870,
                    150,
                    1,
                    false,
                    false,
                    false,
                    10
                ),
            ]
        );

        $service = $container->get(CreatePackageService::class);

        $packages = $service->create($store->getProducts()->toArray(), 100000.0, 1000, 1000, 1000);

        $this->assertNotEmpty($packages);
        $this->assertIsArray($packages);
        $this->assertCount(1, $packages);

        $package = reset($packages);

        $this->assertNotNull($package);
        $this->assertInstanceOf(Package::class, $package);
    }

    public function testCreatePackageIfEqualDeliveryPeriod()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');
        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $city = CityFixture::getOne($region, 'city', 'Moskva');

        $address = AddressFixture::getOneFromAddressDto($city, DaDataAddressDtoFixture::getOne());
        $contact = ContactFixture::getOne('test@gmail.com', 'test', ['+777777777']);

        $store = StoreFixture::getOneFilled(
            $contact,
            $address,
            1,
            products: [
                ProductFixture::getOne(
                    'AA-1234',
                    'desc',
                    '1000.0',
                    104000,
                    360,
                    870,
                    150,
                    1,
                    false,
                    false,
                    false,
                    1
                ),
                ProductFixture::getOne(
                    'AA-4321',
                    'desc',
                    '2000.0',
                    21200,
                    1260,
                    570,
                    350,
                    1,
                    false,
                    false,
                    false,
                    1
                ),
                ProductFixture::getOne(
                    'AA-5678',
                    'desc',
                    '2000.0',
                    30000,
                    500,
                    500,
                    500,
                    1,
                    false,
                    false,
                    false,
                    1
                ),
                ProductFixture::getOne(
                    'AA-5680',
                    'desc',
                    '2000.0',
                    50000,
                    500,
                    500,
                    500,
                    3,
                    false,
                    false,
                    false,
                    1
                ),
            ]
        );

        $service = $container->get(CreatePackageService::class);

        $packages = $service->create($store->getProducts()->toArray(), 100000.0, 1000, 1000, 1000);

        $this->assertNotEmpty($packages);
        $this->assertIsArray($packages);
        $this->assertCount(4, $packages);

        $package = reset($packages);

        $this->assertNotNull($package);
        $this->assertInstanceOf(Package::class, $package);
    }

    public function testCreatePackageIfCanRotateProduct()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');
        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $city = CityFixture::getOne($region, 'city', 'Moskva');

        $address = AddressFixture::getOneFromAddressDto($city, DaDataAddressDtoFixture::getOne());
        $contact = ContactFixture::getOne('test@gmail.com', 'test', ['+777777777']);

        $store = StoreFixture::getOneFilled(
            $contact,
            $address,
            1,
            products: [
                ProductFixture::getOne(
                    'AA-4321',
                    'desc',
                    '2000.0',
                    30000,
                    600, // ширина (мм)
                    500, // высота (мм)
                    1200, // длина (мм)
                    1,
                    false,
                    false,
                    true,
                    1
                ),
                ProductFixture::getOne(
                    'AA-4321',
                    'desc',
                    '2000.0',
                    30000,
                    1200, // ширина (мм)
                    550, // высота (мм)
                    600, // длина (мм)
                    1,
                    false,
                    false,
                    true,
                    1
                ),
                ProductFixture::getOne(
                    'AA-4321',
                    'desc',
                    '2000.0',
                    30000,
                    600, // ширина (мм)
                    1200, // высота (мм)
                    600, // длина (мм)
                    1,
                    false,
                    false,
                    true,
                    1
                ),
            ]
        );

        $service = $container->get(CreatePackageService::class);

        $packages = $service->create($store->getProducts()->toArray(), 100000.0, 1000, 1000, 1000);

        $this->assertNotEmpty($packages);
        $this->assertIsArray($packages);
        $this->assertCount(3, $packages);

        $package = reset($packages);

        $this->assertNotNull($package);
        $this->assertInstanceOf(Package::class, $package);
    }

    public function testCreatePackageWithFillingTheSpaceToTheMaximum()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');
        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $city = CityFixture::getOne($region, 'city', 'Moskva');

        $address = AddressFixture::getOneFromAddressDto($city, DaDataAddressDtoFixture::getOne());
        $contact = ContactFixture::getOne('test@gmail.com', 'test', ['+777777777']);

        $store = StoreFixture::getOneFilled(
            $contact,
            $address,
            1,
            products: [
                ProductFixture::getOne(
                    'AA-1234',
                    'desc',
                    '1000.0',
                    35000,
                    800,
                    300,
                    400,
                    3,
                    false,
                    false,
                    false,
                    1
                ),
                ProductFixture::getOne(
                    'AA-1234',
                    'desc',
                    '1000.0',
                    30000,
                    300,
                    800,
                    400,
                    2,
                    false,
                    false,
                    false,
                    1
                ),
            ]
        );

        $service = $container->get(CreatePackageService::class);

        $packages = $service->create($store->getProducts()->toArray(), 100000.0, 1000, 1000, 1000);

        $this->assertNotEmpty($packages);
        $this->assertIsArray($packages);
        $this->assertCount(3, $packages);

        $package = reset($packages);

        $this->assertNotNull($package);
        $this->assertInstanceOf(Package::class, $package);
    }

    public function testCreatePackageWithOneProduct()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');
        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $city = CityFixture::getOne($region, 'city', 'Moskva');

        $address = AddressFixture::getOneFromAddressDto($city, DaDataAddressDtoFixture::getOne());
        $contact = ContactFixture::getOne('test@gmail.com', 'test', ['+777777777']);

        $store = StoreFixture::getOneFilled(
            $contact,
            $address,
            1,
            products: [
                ProductFixture::getOne(
                    'AA-1234',
                    'desc',
                    '1000.0',
                    5430,
                    300,
                    110,
                    440,
                    13,
                    false,
                    false,
                    false,
                    1
                ),
            ]
        );

        $service = $container->get(CreatePackageService::class);

        $packages = $service->create($store->getProducts()->toArray(), 100000.0, 1000, 1000, 1000);

        $this->assertNotEmpty($packages);
        $this->assertIsArray($packages);
        $this->assertCount(2, $packages);

        $firstPackage = reset($packages);

        $this->assertNotNull($firstPackage);
        $this->assertInstanceOf(Package::class, $firstPackage);

        $this->assertEquals(65160, $firstPackage->getWeight());

        $this->assertEquals(880, $firstPackage->getLength());
        $this->assertEquals(900, $firstPackage->getWidth());
        $this->assertEquals(990, $firstPackage->getHeight());

        $this->assertEquals(12, $firstPackage->getProducts()->first()->getQuantity());

        $lastPackage = end($packages);

        $this->assertNotNull($lastPackage);
        $this->assertInstanceOf(Package::class, $lastPackage);

        $this->assertEquals(1, $lastPackage->getProducts()->first()->getQuantity());

        $this->assertEquals(5430, $lastPackage->getWeight());

        $this->assertEquals(440, $lastPackage->getLength());
        $this->assertEquals(300, $lastPackage->getWidth());
        $this->assertEquals(110, $lastPackage->getHeight());
    }
}
