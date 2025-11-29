<?php

namespace App\Tests\Infrastructure\Http\Tax\v1;

use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Tax\Entity\Tax;
use App\Domain\Tax\Repository\TaxRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Tax\TaxFixture;
use App\Tests\HttpTestCase;

class DeleteTaxControllerTest extends HttpTestCase
{
    public function testDeleteTaxRoute()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $repository = $container->get(TaxRepositoryInterface::class);
        $newTax = TaxFixture::getOne($country, 'НДС', 0.2, 'price/(1+value)*value');

        $repository->create($newTax);

        $this->client->request(
            'DElETE',
            "/api/v1/tax/{$newTax->getId()->toRfc4122()}"
        );

        self::assertResponseStatusCodeSame(204);

        $deletedTax = $repository->ofIdDeleted($newTax->getId());

        $this->assertNotNull($deletedTax);
        $this->assertInstanceOf(Tax::class, $deletedTax);
        $this->assertInstanceOf(Country::class, $deletedTax->getCountry());
        $this->assertEquals('НДС', $deletedTax->getName());
        $this->assertEquals('0.2', $deletedTax->getValue());
        $this->assertEquals('price/(1+value)*value', $deletedTax->getExpression());
        $this->assertNotNull($deletedTax->getCreatedAt());
        $this->assertNull($deletedTax->getUpdatedAt());
        $this->assertNotNull($deletedTax->getDeletedAt());
    }
}
