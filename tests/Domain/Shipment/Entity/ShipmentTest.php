<?php

namespace App\Tests\Domain\Shipment\Entity;

use App\Domain\Address\Entity\Address;
use App\Domain\Contact\Entity\Contact;
use App\Domain\Currency\Entity\Currency;
use App\Domain\Shipment\Entity\Package;
use App\Domain\Shipment\Entity\PackageProduct;
use App\Domain\Shipment\Entity\Product;
use App\Domain\Shipment\Entity\Shipment;
use App\Domain\Shipment\Entity\Store;
use App\Domain\Shipment\Entity\StoreSchedule;
use App\Domain\Shipment\ValueObject\Day;
use App\Domain\Shipment\ValueObject\EndTime;
use App\Domain\Shipment\ValueObject\StartTime;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\Address\PointValueFixture;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Contact\ContactFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\DaData\DaDataAddressDtoFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\Fixture\Shipment\PackageFixture;
use App\Tests\Fixture\Shipment\PackageProductFixture;
use App\Tests\Fixture\Shipment\ProductFixture;
use App\Tests\Fixture\Shipment\ShipmentFixture;
use App\Tests\Fixture\Shipment\StoreFixture;
use App\Tests\Fixture\Shipment\StoreScheduleFixture;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ShipmentTest extends KernelTestCase
{
    public function testCreateShipment()
    {
        $shipment = ShipmentFixture::getOneFilled();

        $this->assertNotNull($shipment);
        $this->assertInstanceOf(Shipment::class, $shipment);
        $this->assertInstanceOf(Address::class, $shipment->getFrom());
        $this->assertInstanceOf(Address::class, $shipment->getTo());
        $this->assertInstanceOf(Contact::class, $shipment->getSender());
        $this->assertInstanceOf(Contact::class, $shipment->getRecipient());
        $this->assertInstanceOf(Currency::class, $shipment->getCurrency());
        $this->assertInstanceOf(Collection::class, $shipment->getPackages());
        $this->assertInstanceOf(\DateTime::class, $shipment->getPsd());
        $this->assertNotNull($shipment->getPsd());
        $this->assertNotNull($shipment->getCreatedAt());
        $this->assertNotNull($shipment->getUpdatedAt());
    }

    public function testAddPackageToShipment()
    {
        $shipment = ShipmentFixture::getOneFilled();

        $this->assertNotNull($shipment);
        $this->assertInstanceOf(Shipment::class, $shipment);
        $this->assertInstanceOf(Collection::class, $shipment->getPackages());
        $this->assertNotNull($shipment->getCreatedAt());
        $this->assertNotNull($shipment->getUpdatedAt());

        $package = PackageFixture::getOne(2, 2, 2, 2, 2);
        $shipment->addPackage($package);

        $this->assertNotEmpty($shipment->getPackages());
        $this->assertTrue($shipment->getPackages()->contains($package));
    }

    public function testRemovePackageFromShipment()
    {
        $shipment = ShipmentFixture::getOneFilled();

        $this->assertNotNull($shipment);
        $this->assertInstanceOf(Shipment::class, $shipment);
        $this->assertInstanceOf(Collection::class, $shipment->getPackages());
        $this->assertNotNull($shipment->getCreatedAt());
        $this->assertNotNull($shipment->getUpdatedAt());

        $package = PackageFixture::getOne(2, 2, 2, 2, 2);
        $shipment->addPackage($package);

        $this->assertNotEmpty($shipment->getPackages());
        $this->assertTrue($shipment->getPackages()->contains($package));

        $shipment->removePackage($package);

        $this->assertFalse($shipment->getPackages()->contains($package));
    }

    public function testChangeShipmentFromAddress()
    {
        $shipment = ShipmentFixture::getOneFilled();

        $this->assertNotNull($shipment);
        $this->assertInstanceOf(Shipment::class, $shipment);
        $this->assertInstanceOf(Address::class, $shipment->getFrom());
        $this->assertNotNull($shipment->getCreatedAt());
        $this->assertNotNull($shipment->getUpdatedAt());

        $fromAddress = AddressFixture::getOne(
            CityFixture::getOne(
                RegionFixture::getOne(
                    CountryFixture::getOne('Russia', 'RU'),
                    'Moscow',
                    'msk'
                ),
                'Город',
                'Moscow',
            ),
            'Lenin street 2/2',
            '4/4',
            PointValueFixture::getOne(42.2313, 43.2323),
            '1234567',
            'Lenin'
        );

        $shipment->changeFrom($fromAddress);

        $this->assertEquals($fromAddress, $shipment->getFrom());
    }

    public function testChangeShipmentToAddress()
    {
        $shipment = ShipmentFixture::getOneFilled();

        $this->assertNotNull($shipment);
        $this->assertInstanceOf(Shipment::class, $shipment);
        $this->assertInstanceOf(Address::class, $shipment->getTo());
        $this->assertNotNull($shipment->getCreatedAt());
        $this->assertNotNull($shipment->getUpdatedAt());

        $toAddress = AddressFixture::getOne(
            CityFixture::getOne(
                RegionFixture::getOne(
                    CountryFixture::getOne('Russia', 'RU'),
                    'Moscow',
                    'msk'
                ),
                'Город',
                'Moscow',
            ),
            'Lenin street 2/2',
            '5/5',
            PointValueFixture::getOne(42.2313, 43.2323),
            '1234567',
            'Lenin'
        );

        $shipment->changeTo($toAddress);

        $this->assertEquals($toAddress, $shipment->getTo());
    }

    public function testChangeShipmentSender()
    {
        $shipment = ShipmentFixture::getOneFilled();

        $this->assertNotNull($shipment);
        $this->assertInstanceOf(Shipment::class, $shipment);
        $this->assertInstanceOf(Contact::class, $shipment->getSender());
        $this->assertNotNull($shipment->getCreatedAt());
        $this->assertNotNull($shipment->getUpdatedAt());

        $sender = ContactFixture::getOne('test-updated@gmail.com', 'sender');

        $shipment->changeSender($sender);

        $this->assertEquals($sender, $shipment->getSender());
    }

    public function testChangeShipmentRecipient()
    {
        $shipment = ShipmentFixture::getOneFilled();

        $this->assertNotNull($shipment);
        $this->assertInstanceOf(Shipment::class, $shipment);
        $this->assertInstanceOf(Contact::class, $shipment->getRecipient());
        $this->assertNotNull($shipment->getCreatedAt());
        $this->assertNotNull($shipment->getUpdatedAt());

        $recipient = ContactFixture::getOne('test-updated@gmail.com', 'sender');

        $shipment->changeRecipient($recipient);

        $this->assertEquals($recipient, $shipment->getRecipient());
    }

    public function testChangeShipmentPsd()
    {
        $shipment = ShipmentFixture::getOneFilled(psd: new \DateTime('now'));

        $this->assertNotNull($shipment);
        $this->assertInstanceOf(Shipment::class, $shipment);
        $this->assertInstanceOf(\DateTime::class, $shipment->getPsd());
        $this->assertNotNull($shipment->getCreatedAt());
        $this->assertNotNull($shipment->getUpdatedAt());

        $newPsd = new \DateTime('+1 day');

        $shipment->changePsd($newPsd);

        $this->assertTrue($shipment->equalsPsd($newPsd));
    }

    public function testAddPackageWithProductAndStore()
    {
        $newProduct = ProductFixture::getOne('AA-1234', 'desc', '100.0', 1, 1, 1, 1, 1, false, false, false, 1);

        $country = CountryFixture::getOne('Russia', 'RU');
        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $city = CityFixture::getOne($region, 'city', 'Moskva');

        $newAddress = AddressFixture::getOneFromAddressDto($city, DaDataAddressDtoFixture::getOne());
        $newContact = ContactFixture::getOne('test@gmail.com', 'test', ['+777777777']);
        $newStore = StoreFixture::getOneFilled($newContact, $newAddress, 1, 100, 100, 100);
        $newStore->addProduct($newProduct);

        $newStoreSchedule = StoreScheduleFixture::getOneFilled(1, '10:00:00', '19:00:00');
        $newStore->addSchedule($newStoreSchedule);

        $newPackage = PackageFixture::getOne(1, 1, 1, 1, 1);
        $newPackageProduct = PackageProductFixture::getOne(1, $newProduct);
        $newPackage->addProduct($newPackageProduct);

        $shipment = ShipmentFixture::getOneFilled(packages: [$newPackage]);

        $this->assertNotNull($shipment);
        $this->assertInstanceOf(Shipment::class, $shipment);
        $this->assertNotEmpty($shipment->getPackages());
        $this->assertNotNull($shipment->getCreatedAt());
        $this->assertNotNull($shipment->getUpdatedAt());

        $package = $shipment->getPackages()->first();

        $this->assertNotNull($package);
        $this->assertInstanceOf(Package::class, $package);
        $this->assertEquals(1, $package->getPrice());
        $this->assertEquals(1, $package->getWidth());
        $this->assertEquals(1, $package->getHeight());
        $this->assertEquals(1, $package->getLength());
        $this->assertEquals(1, $package->getWeight());

        $this->assertNotEmpty($package->getProducts());

        $packageProduct = $package->getProducts()->first();

        $this->assertNotNull($packageProduct);
        $this->assertInstanceOf(PackageProduct::class, $packageProduct);
        $this->assertInstanceOf(Product::class, $packageProduct->getProduct());
        $this->assertInstanceOf(Package::class, $packageProduct->getPackage());
        $this->assertEquals('AA-1234', $packageProduct->getProduct()->getCode());
        $this->assertEquals(100.0, $packageProduct->getProduct()->getPrice());
        $this->assertEquals($package, $packageProduct->getPackage());
        $this->assertInstanceOf(Store::class, $packageProduct->getProduct()->getStore());
        $this->assertNotNull($packageProduct->getProduct()->getStore());
        $this->assertEquals(1, $packageProduct->getProduct()->getWeight());
        $this->assertEquals(1, $packageProduct->getProduct()->getVolume());
        $this->assertEquals(1, $packageProduct->getProduct()->getLength());
        $this->assertEquals(1, $packageProduct->getProduct()->getQuantity());
        $this->assertFalse($packageProduct->getProduct()->isFragile());
        $this->assertFalse($packageProduct->getProduct()->isFlammable());
        $this->assertFalse($packageProduct->getProduct()->isCanRotate());
        $this->assertEquals(1, $packageProduct->getProduct()->getDeliveryPeriod());

        $store = $packageProduct->getProduct()->getStore();
        $this->assertNotNull($store);
        $this->assertInstanceOf(Store::class, $store);
        $this->assertInstanceOf(Contact::class, $store->getContact());
        $this->assertNotNull($store->getContact());
        $this->assertInstanceOf(Address::class, $store->getAddress());
        $this->assertNotNull($store->getAddress());
        $this->assertNotEmpty($store->getProducts());
        $this->assertNotEmpty($store->getSchedules());
        $this->assertEquals(1, $store->getExternalId());
        $this->assertEquals(100, $store->getMaxWeight());
        $this->assertEquals(100, $store->getMaxVolume());
        $this->assertEquals(100, $store->getMaxLength());

        $schedules = $store->getSchedules();

        $this->assertNotEmpty($schedules);

        $schedule = $schedules->first();

        $this->assertNotNull($schedule);
        $this->assertInstanceOf(StoreSchedule::class, $schedule);
        $this->assertInstanceOf(Day::class, $schedule->getDay());
        $this->assertEquals(1, $schedule->getDay()->getValue());
        $this->assertInstanceOf(StartTime::class, $schedule->getStartTime());
        $this->assertEquals('10:00:00', $schedule->getStartTime()->getValue());
        $this->assertInstanceOf(EndTime::class, $schedule->getEndTime());
        $this->assertEquals('19:00:00', $schedule->getEndTime()->getValue());

        $this->assertEquals($store, $schedule->getStore());
    }
}
