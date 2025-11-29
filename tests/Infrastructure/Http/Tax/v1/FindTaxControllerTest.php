<?php

namespace App\Tests\Infrastructure\Http\Tax\v1;

use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Tax\Repository\TaxRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Tax\TaxFixture;
use App\Tests\HttpTestCase;

class FindTaxControllerTest extends HttpTestCase
{
    public function testFindById()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $newTax = TaxFixture::getOne($country, 'NDS', 0.2, 'price/(1+value)*value');

        $repository = $this->getContainer()->get(TaxRepositoryInterface::class);
        $repository->create($newTax);

        $this->client->request(
            'GET',
            "/api/v1/tax/find-by-id/{$newTax->getId()->toRfc4122()}"
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('country', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('value', $response);
        $this->assertStringContainsString('expression', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);
        $this->assertStringContainsString('deletedAt', $response);

        $this->assertStringContainsString('RU', $response);
        $this->assertStringContainsString('Russia', $response);
        $this->assertStringContainsString('NDS', $response);
    }

    public function testFindByCountry()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $newTax = TaxFixture::getOne($country, 'NDS', 0.2, 'price/(1+value)*value');

        $repository = $this->getContainer()->get(TaxRepositoryInterface::class);
        $repository->create($newTax);

        $this->client->request(
            'GET',
            "/api/v1/tax/find-by-country/{$country->getCode()}"
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('country', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('value', $response);
        $this->assertStringContainsString('expression', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);
        $this->assertStringContainsString('deletedAt', $response);

        $this->assertStringContainsString('RU', $response);
        $this->assertStringContainsString('Russia', $response);
        $this->assertStringContainsString('NDS', $response);
    }
}
