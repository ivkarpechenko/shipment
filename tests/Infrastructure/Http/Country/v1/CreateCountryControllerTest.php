<?php

namespace App\Tests\Infrastructure\Http\Country\v1;

use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Tests\HttpTestCase;

class CreateCountryControllerTest extends HttpTestCase
{
    public function testCreateCountryRoute()
    {
        $this->client->request(
            'POST',
            '/api/v1/country',
            [
                'name' => 'test country',
                'code' => 'RU',
            ]
        );

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(201);

        $repository = $this->getContainer()->get(CountryRepositoryInterface::class);

        $country = $repository->ofCode('RU');

        $this->assertNotNull($country);
        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals('test country', $country->getName());
        $this->assertEquals('RU', $country->getCode());
        $this->assertNotNull($country->getCreatedAt());
        $this->assertNull($country->getUpdatedAt());
        $this->assertNull($country->getDeletedAt());
    }
}
