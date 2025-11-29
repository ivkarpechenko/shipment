<?php

namespace App\Tests\Infrastructure\Http\Tax\v1;

use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Tax\Entity\Tax;
use App\Domain\Tax\Repository\TaxRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Tax\TaxFixture;
use App\Tests\HttpTestCase;

class RestoreTaxControllerTest extends HttpTestCase
{
    public function testRestoreTaxRoute()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());
        $repository = $this->getContainer()->get(TaxRepositoryInterface::class);
        $newTax = TaxFixture::getOneForDeleted($country, 'NDS', 0.2, 'price/(1+value)*value');

        $repository->create($newTax);

        $this->client->request(
            'POST',
            "/api/v1/tax/{$newTax->getId()->toRfc4122()}/restore"
        );

        self::assertResponseIsSuccessful();

        $restoredTax = $repository->ofId($newTax->getId());

        $this->assertNotNull($restoredTax);
        $this->assertInstanceOf(Tax::class, $restoredTax);
        $this->assertEquals('NDS', $restoredTax->getName());
        $this->assertEquals('0.2', $restoredTax->getValue());
        $this->assertEquals('price/(1+value)*value', $restoredTax->getExpression());
        $this->assertNotNull($restoredTax->getCreatedAt());
        $this->assertNotNull($restoredTax->getUpdatedAt());
        $this->assertNull($restoredTax->getDeletedAt());
    }
}
