<?php

namespace App\Tests\Infrastructure\Http\Country\v1;

use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\HttpTestCase;

class RestoreCountryControllerTest extends HttpTestCase
{
    public function testRestoreCountryRoute()
    {
        $repository = $this->getContainer()->get(CountryRepositoryInterface::class);
        $newCountry = CountryFixture::getOneForDeleted('test country', 'RU');

        $repository->create($newCountry);

        $this->client->request(
            'POST',
            "/api/v1/country/{$newCountry->getId()->toRfc4122()}/restore"
        );

        self::assertResponseIsSuccessful();

        $restoredCountry = $repository->ofId($newCountry->getId());

        $this->assertNotNull($restoredCountry);
        $this->assertInstanceOf(Country::class, $restoredCountry);
        $this->assertEquals('test country', $restoredCountry->getName());
        $this->assertEquals('RU', $restoredCountry->getCode());
        $this->assertNotNull($restoredCountry->getCreatedAt());
        $this->assertNotNull($restoredCountry->getUpdatedAt());
        $this->assertNull($restoredCountry->getDeletedAt());
    }
}
