<?php

namespace App\Tests\Fixture\Shipment;

use App\Domain\Address\Entity\Address;
use App\Domain\Contact\Entity\Contact;
use App\Domain\Shipment\Entity\Product;
use App\Domain\Shipment\Entity\Store;
use App\Domain\Shipment\Entity\StoreSchedule;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Contact\ContactFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\DaData\DaDataAddressDtoFixture;
use App\Tests\Fixture\Region\RegionFixture;
use Symfony\Component\Uid\Uuid;

class StoreFixture
{
    public static function getOne(
        Contact $contact,
        Address $address,
        int $externalId,
        int $maxWeight,
        int $maxVolume,
        int $maxLength,
        bool $isPickup,
        ?\DateTime $psd,
        ?\DateTime $psdStartTime,
        ?\DateTime $psdEndTime
    ): Store {
        $store = new Store($contact, $address, $externalId, $maxWeight, $maxVolume, $maxLength, $isPickup, $psd, $psdStartTime, $psdEndTime);

        $reflectionClass = new \ReflectionClass(Store::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($store, Uuid::v1());

        return $store;
    }

    public static function getOneFilled(
        ?Contact $contact = null,
        ?Address $address = null,
        ?int $externalId = null,
        ?int $maxWeight = null,
        ?int $maxVolume = null,
        ?int $maxLength = null,
        ?bool $isPickup = null,
        // @var Product[] $products
        array $products = [],
        // @var StoreSchedule[] $schedules
        array $schedules = [],
        ?\DateTime $psd = null,
        ?\DateTime $psdStartTime = null,
        ?\DateTime $psdEndTime = null
    ): Store {
        if (is_null($address)) {
            $country = CountryFixture::getOne('Russia', 'RU');
            $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
            $city = CityFixture::getOne($region, 'city', 'Moskva');

            $address = AddressFixture::getOneFromAddressDto($city, DaDataAddressDtoFixture::getOne());
        }

        if (is_null($contact)) {
            $contact = ContactFixture::getOne('test@gmail.com', 'test', ['+777777777']);
        }

        $store = self::getOne(
            $contact,
            $address,
            $externalId ?? 0,
            $maxWeight ?? 0,
            $maxVolume ?? 0,
            $maxLength ?? 0,
            $isPickup ?? false,
            $psd ?? (new \DateTime()),
            $psdStartTime ?? (new \DateTime()),
            $psdEndTime ?? (new \DateTime())
        );

        foreach ($products as $product) {
            $store->addProduct($product);
        }

        foreach ($schedules as $schedule) {
            $store->addSchedule($schedule);
        }

        return $store;
    }
}
