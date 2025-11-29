<?php

namespace App\Tests\Domain\Currency\Entity;

use App\Domain\Currency\Entity\Currency;
use App\Tests\Fixture\Currency\CurrencyFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CurrencyTest extends KernelTestCase
{
    public function testCreateCurrency()
    {
        $currency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals('RUB', $currency->getCode());
        $this->assertEquals(810, $currency->getNum());
        $this->assertEquals('Russian ruble', $currency->getName());
        $this->assertTrue($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNull($currency->getUpdatedAt());
    }

    public function testUpdateCurrency()
    {
        $currency = CurrencyFixture::getOne('RUB', 810, 'Russian ruble');

        $this->assertNotNull($currency);
        $this->assertInstanceOf(Currency::class, $currency);
        $this->assertEquals('RUB', $currency->getCode());
        $this->assertEquals(810, $currency->getNum());
        $this->assertEquals('Russian ruble', $currency->getName());
        $this->assertTrue($currency->isActive());
        $this->assertNotNull($currency->getCreatedAt());
        $this->assertNull($currency->getUpdatedAt());

        $currency->changeName('Updated russian ruble');

        $this->assertEquals('Updated russian ruble', $currency->getName());
        $this->assertNotNull($currency->getUpdatedAt());

        $currency->changeIsActive(false);

        $this->assertFalse($currency->isActive());
        $this->assertNotNull($currency->getUpdatedAt());
    }
}
