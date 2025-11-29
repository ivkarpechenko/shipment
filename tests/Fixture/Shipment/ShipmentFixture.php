<?php

namespace App\Tests\Fixture\Shipment;

use App\Domain\Address\Entity\Address;
use App\Domain\Contact\Entity\Contact;
use App\Domain\Currency\Entity\Currency;
use App\Domain\Shipment\Entity\Shipment;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\Address\PointValueFixture;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Contact\ContactFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Currency\CurrencyFixture;
use App\Tests\Fixture\Region\RegionFixture;
use Symfony\Component\Uid\Uuid;

class ShipmentFixture
{
    public static function getOne(
        Address $from,
        Address $to,
        Contact $sender,
        Contact $recipient,
        Currency $currency,
        \DateTime $psd,
        \DateTime $psdStartTime,
        \DateTime $psdEndTime
    ): Shipment {
        $shipment = new Shipment(
            $from,
            $to,
            $sender,
            $recipient,
            $currency,
            $psd,
            $psdStartTime,
            $psdEndTime
        );

        $reflectionClass = new \ReflectionClass(Shipment::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($shipment, Uuid::v1());

        return $shipment;
    }

    public static function getOneFilled(
        ?Address $from = null,
        ?Address $to = null,
        ?Contact $sender = null,
        ?Contact $recipient = null,
        ?Currency $currency = null,
        array $packages = [],
        ?\DateTime $psd = null,
        ?\DateTime $psdStartTime = null,
        ?\DateTime $psdEndTime = null,
    ): Shipment {
        $shipment = new Shipment(
            $from ?: AddressFixture::getOne(
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
                '2/2',
                PointValueFixture::getOne(42.2324, 43.3242),
                '1234567',
                'Lenin'
            ),
            $to ?: AddressFixture::getOne(
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
                '2/2',
                PointValueFixture::getOne(42.2324, 43.3242),
                '1234567',
                'Lenin'
            ),
            $sender ?: ContactFixture::getOne('test@gmail.com', 'sender'),
            $recipient ?: ContactFixture::getOne('test@gmail.com', 'recipient'),
            $currency ?: CurrencyFixture::getOne('RUB', 810, 'Russian ruble'),
            $psd ?: new \DateTime('now'),
            $psdStartTime ?: new \DateTime('now'),
            $psdEndTime ?: new \DateTime('now'),
        );

        if ($packages) {
            foreach ($packages as $package) {
                $shipment->addPackage($package);
            }
        } else {
            $shipment->addPackage(PackageFixture::getOne(1, 1, 1, 1, 1));
        }

        $reflectionClass = new \ReflectionClass(Shipment::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($shipment, Uuid::v1());

        return $shipment;
    }
}
