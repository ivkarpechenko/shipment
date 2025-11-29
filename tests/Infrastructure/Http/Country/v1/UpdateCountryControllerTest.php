<?php

namespace App\Tests\Infrastructure\Http\Country\v1;

use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\HttpTestCase;

class UpdateCountryControllerTest extends HttpTestCase
{
    public function testUpdateCountryNameRoute()
    {
        $repository = $this->getContainer()->get(CountryRepositoryInterface::class);
        $newCountry = CountryFixture::getOne('test country', 'RU');

        $repository->create($newCountry);

        $this->assertEquals('test country', $newCountry->getName());

        $this->client->request(
            'PUT',
            "/api/v1/country/{$newCountry->getId()->toRfc4122()}",
            [
                'name' => 'updated country',
            ]
        );

        self::assertResponseIsSuccessful();

        $country = $repository->ofId($newCountry->getId());

        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals('updated country', $country->getName());
        $this->assertEquals('RU', $country->getCode());
        $this->assertNotNull($country->getCreatedAt());
        $this->assertNotNull($country->getUpdatedAt());
        $this->assertNull($country->getDeletedAt());
    }

    public function testUpdateCountryIsActiveRoute()
    {
        $repository = $this->getContainer()->get(CountryRepositoryInterface::class);
        $newCountry = CountryFixture::getOne('test country', 'RU');

        $repository->create($newCountry);

        $this->assertTrue($newCountry->isActive());

        $this->client->request(
            'PUT',
            "/api/v1/country/{$newCountry->getId()->toRfc4122()}",
            [
                'isActive' => false,
            ]
        );

        self::assertResponseIsSuccessful();

        $country = $repository->ofIdDeactivated($newCountry->getId());

        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals('test country', $country->getName());
        $this->assertEquals('RU', $country->getCode());
        $this->assertFalse($country->isActive());
        $this->assertNotNull($country->getCreatedAt());
        $this->assertNotNull($country->getUpdatedAt());
        $this->assertNull($country->getDeletedAt());
    }
}
