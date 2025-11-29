<?php

namespace App\Tests\Infrastructure\Http\Tax\v1;

use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Tax\Entity\Tax;
use App\Domain\Tax\Repository\TaxRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Tax\TaxFixture;
use App\Tests\HttpTestCase;

class UpdateTaxControllerTest extends HttpTestCase
{
    public function testUpdateTaxNameRoute()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $repository = $container->get(TaxRepositoryInterface::class);
        $newTax = TaxFixture::getOne($country, 'NDS', 0.2, 'price/(1+value)*value');

        $repository->create($newTax);

        $this->assertEquals('NDS', $newTax->getName());

        $this->client->request(
            'PUT',
            "/api/v1/tax/{$newTax->getId()->toRfc4122()}",
            [
                'value' => 0.18,
            ]
        );

        self::assertResponseIsSuccessful();

        $tax = $repository->ofId($newTax->getId());

        $this->assertNotNull($tax);
        $this->assertInstanceOf(Tax::class, $tax);
        $this->assertEquals('NDS', $tax->getName());
        $this->assertEquals(0.18, $tax->getValue());
        $this->assertEquals('price/(1+value)*value', $tax->getExpression());
        $this->assertNotNull($tax->getCreatedAt());
        $this->assertNotNull($tax->getUpdatedAt());
        $this->assertNull($tax->getDeletedAt());
    }
}
