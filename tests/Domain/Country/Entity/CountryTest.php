<?php

namespace App\Tests\Domain\Country\Entity;

use App\Tests\Fixture\Country\CountryFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CountryTest extends KernelTestCase
{
    public function testCreateCountry()
    {
        $country = CountryFixture::getOne('test country', 'RU');

        $this->assertEquals('test country', $country->getName());
        $this->assertEquals('RU', $country->getCode());
        $this->assertTrue($country->isActive());
        $this->assertNotNull($country->getCreatedAt());
    }

    public function testUpdateCountryName()
    {
        $country = CountryFixture::getOne('test country', 'RU');

        $this->assertEquals('test country', $country->getName());
        $this->assertEquals('RU', $country->getCode());
        $this->assertTrue($country->isActive());
        $this->assertNotNull($country->getCreatedAt());

        $country->changeName('new country');

        $this->assertEquals('new country', $country->getName());
        $this->assertNotNull($country->getUpdatedAt());
    }

    public function testUpdateCountryIsActive()
    {
        $country = CountryFixture::getOne('test country', 'RU');

        $this->assertEquals('test country', $country->getName());
        $this->assertEquals('RU', $country->getCode());
        $this->assertTrue($country->isActive());
        $this->assertNotNull($country->getCreatedAt());

        $country->changeIsActive(false);

        $this->assertFalse($country->isActive());
        $this->assertNotNull($country->getUpdatedAt());
    }
}
