<?php

namespace App\Tests\Domain\Address\Entity;

use App\Domain\City\Entity\City;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\DaData\DaDataAddressDtoFixture;
use App\Tests\Fixture\Region\RegionFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AddressTest extends KernelTestCase
{
    public function testCreateAddress()
    {
        $country = CountryFixture::getOne('Russia', 'RU');
        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $city = CityFixture::getOne($region, 'city', 'Moskva');

        $address = AddressFixture::getOneFromAddressDto($city, DaDataAddressDtoFixture::getOne());

        $this->assertNotNull($address->getCity());
        $this->assertInstanceOf(City::class, $address->getCity());
        $this->assertEquals('309850', $address->getPostalCode());
        $this->assertEquals('ул Слободская', $address->getStreet());
        $this->assertEquals('1/1', $address->getHouse());
        $this->assertTrue($address->isActive());
        $this->assertNotNull($address->getCreatedAt());
    }

    public function testUpdateAddressStreet()
    {
        $country = CountryFixture::getOne('Russia', 'RU');
        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $city = CityFixture::getOne($region, 'city', 'Moskva');

        $address = AddressFixture::getOneFromAddressDto($city, DaDataAddressDtoFixture::getOne());

        $this->assertNotNull($address->getCity());
        $this->assertInstanceOf(City::class, $address->getCity());
        $this->assertEquals('309850', $address->getPostalCode());
        $this->assertEquals('ул Слободская', $address->getStreet());
        $this->assertEquals('1/1', $address->getHouse());
        $this->assertTrue($address->isActive());
        $this->assertNotNull($address->getCreatedAt());

        $address->changeStreet('Lenina2');

        $this->assertEquals('Lenina2', $address->getStreet());
        $this->assertNotNull($address->getUpdatedAt());
    }

    public function testUpdateAddressIsActive()
    {
        $country = CountryFixture::getOne('Russia', 'RU');
        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $city = CityFixture::getOne($region, 'city', 'Moskva');

        $address = AddressFixture::getOneFromAddressDto($city, DaDataAddressDtoFixture::getOne());

        $this->assertNotNull($address->getCity());
        $this->assertInstanceOf(City::class, $address->getCity());
        $this->assertInstanceOf(City::class, $address->getCity());
        $this->assertEquals('309850', $address->getPostalCode());
        $this->assertEquals('ул Слободская', $address->getStreet());
        $this->assertEquals('1/1', $address->getHouse());
        $this->assertTrue($address->isActive());
        $this->assertNotNull($address->getCreatedAt());

        $address->changeIsActive(false);

        $this->assertFalse($address->isActive());
        $this->assertNotNull($address->getUpdatedAt());
    }
}
