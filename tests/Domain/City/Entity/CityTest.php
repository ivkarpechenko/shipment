<?php

namespace App\Tests\Domain\City\Entity;

use App\Domain\Region\Entity\Region;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CityTest extends KernelTestCase
{
    public function testCreateCity()
    {
        $country = CountryFixture::getOne('Russia', 'RU');
        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');

        $city = CityFixture::getOne($region, 'city', 'Moskva');

        $this->assertNotNull($city->getRegion());
        $this->assertInstanceOf(Region::class, $city->getRegion());
        $this->assertEquals('city', $city->getType());
        $this->assertEquals('Moskva', $city->getName());
        $this->assertTrue($city->isActive());
        $this->assertNotNull($city->getCreatedAt());
    }

    public function testUpdateCityName()
    {
        $country = CountryFixture::getOne('Russia', 'RU');
        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');

        $city = CityFixture::getOne($region, 'city', 'Moskva');

        $this->assertNotNull($city->getRegion());
        $this->assertInstanceOf(Region::class, $city->getRegion());
        $this->assertEquals('city', $city->getType());
        $this->assertEquals('Moskva', $city->getName());
        $this->assertTrue($city->isActive());
        $this->assertNotNull($city->getCreatedAt());

        $city->changeName('Moskva2');

        $this->assertEquals('Moskva2', $city->getName());
        $this->assertNotNull($city->getUpdatedAt());
    }

    public function testUpdateCityIsActive()
    {
        $country = CountryFixture::getOne('Russia', 'RU');
        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');

        $city = CityFixture::getOne($region, 'city', 'Moskva');

        $this->assertNotNull($city->getRegion());
        $this->assertInstanceOf(Region::class, $city->getRegion());
        $this->assertEquals('city', $city->getType());
        $this->assertEquals('Moskva', $city->getName());
        $this->assertTrue($city->isActive());
        $this->assertNotNull($city->getCreatedAt());

        $city->changeIsActive(false);

        $this->assertFalse($city->isActive());
        $this->assertNotNull($city->getUpdatedAt());
    }
}
