<?php

namespace App\Tests\Domain\Region\Entity;

use App\Domain\Country\Entity\Country;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RegionTest extends KernelTestCase
{
    public function testCreateRegion()
    {
        $country = CountryFixture::getOne('Russia', 'RU');

        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');

        $this->assertNotNull($region->getCountry());
        $this->assertInstanceOf(Country::class, $region->getCountry());
        $this->assertEquals('Moskva', $region->getName());
        $this->assertEquals('MOW', $region->getCode());
        $this->assertTrue($region->isActive());
        $this->assertNotNull($region->getCreatedAt());
    }

    public function testUpdateRegionName()
    {
        $country = CountryFixture::getOne('Russia', 'RU');

        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');

        $this->assertNotNull($region->getCountry());
        $this->assertInstanceOf(Country::class, $region->getCountry());
        $this->assertEquals('Moskva', $region->getName());
        $this->assertEquals('MOW', $region->getCode());
        $this->assertTrue($region->isActive());
        $this->assertNotNull($region->getCreatedAt());

        $region->changeName('Moskva2');

        $this->assertEquals('Moskva2', $region->getName());
        $this->assertNotNull($region->getUpdatedAt());
    }

    public function testUpdateRegionIsActive()
    {
        $country = CountryFixture::getOne('Russia', 'RU');

        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');

        $this->assertNotNull($region->getCountry());
        $this->assertInstanceOf(Country::class, $region->getCountry());
        $this->assertEquals('Moskva', $region->getName());
        $this->assertEquals('MOW', $region->getCode());
        $this->assertTrue($region->isActive());
        $this->assertNotNull($region->getCreatedAt());

        $region->changeIsActive(false);

        $this->assertFalse($region->isActive());
        $this->assertNotNull($region->getUpdatedAt());
    }
}
