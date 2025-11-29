<?php

namespace App\Tests\Domain\Tax\Entity;

use App\Domain\Country\Entity\Country;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Tax\TaxFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaxTest extends KernelTestCase
{
    public function testCreateTax()
    {
        $country = CountryFixture::getOne('Russia', 'RU');

        $tax = TaxFixture::getOne($country, 'НДС', 0.2, 'price/(1+value)*value');

        $this->assertNotNull($tax->getCountry());
        $this->assertInstanceOf(Country::class, $tax->getCountry());
        $this->assertEquals('НДС', $tax->getName());
        $this->assertEquals(0.2, $tax->getValue());
        $this->assertEquals('price/(1+value)*value', $tax->getExpression());
        $this->assertNotNull($tax->getCreatedAt());
    }

    public function testUpdateTaxName()
    {
        $country = CountryFixture::getOne('Russia', 'RU');

        $tax = TaxFixture::getOne($country, 'НДС', 0.2, 'price/(1+value)*value');

        $this->assertNotNull($tax->getCountry());
        $this->assertInstanceOf(Country::class, $tax->getCountry());
        $this->assertEquals('НДС', $tax->getName());
        $this->assertEquals(0.2, $tax->getValue());
        $this->assertEquals('price/(1+value)*value', $tax->getExpression());
        $this->assertNotNull($tax->getCreatedAt());

        $tax->changeValue(0.18);

        $this->assertEquals(0.18, $tax->getValue());
        $this->assertNotNull($tax->getUpdatedAt());
    }
}
