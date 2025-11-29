<?php

namespace App\Tests\Infrastructure\Http\Country\v1;

use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\HttpTestCase;

class FindCountryControllerTest extends HttpTestCase
{
    public function testFindById()
    {
        $newCountry = CountryFixture::getOne('test country', 'RU');
        $repository = $this->getContainer()->get(CountryRepositoryInterface::class);
        $repository->create($newCountry);

        $this->client->request(
            'GET',
            "/api/v1/country/find-by-id/{$newCountry->getId()->toRfc4122()}"
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('code', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);
        $this->assertStringContainsString('deletedAt', $response);

        $this->assertStringContainsString('RU', $response);
    }

    public function testFindByCode()
    {
        $newCountry = CountryFixture::getOne('test country', 'RU');
        $repository = $this->getContainer()->get(CountryRepositoryInterface::class);
        $repository->create($newCountry);

        $this->client->request(
            'GET',
            "/api/v1/country/find-by-code/{$newCountry->getCode()}"
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('code', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);
        $this->assertStringContainsString('deletedAt', $response);

        $this->assertStringContainsString('RU', $response);
    }

    public function testFindByName()
    {
        $newCountry = CountryFixture::getOne('test country', 'RU');
        $repository = $this->getContainer()->get(CountryRepositoryInterface::class);
        $repository->create($newCountry);

        $this->client->request(
            'GET',
            "/api/v1/country/find-by-name/{$newCountry->getName()}"
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('code', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);
        $this->assertStringContainsString('deletedAt', $response);

        $this->assertStringContainsString('RU', $response);
    }
}
