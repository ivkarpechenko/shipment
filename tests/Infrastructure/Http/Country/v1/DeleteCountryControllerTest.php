<?php

namespace App\Tests\Infrastructure\Http\Country\v1;

use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\HttpTestCase;

class DeleteCountryControllerTest extends HttpTestCase
{
    public function testDeleteCountryRoute()
    {
        $repository = $this->getContainer()->get(CountryRepositoryInterface::class);
        $newCountry = CountryFixture::getOne('test country', 'RU');

        $repository->create($newCountry);

        $this->client->request(
            'DElETE',
            "/api/v1/country/{$newCountry->getId()->toRfc4122()}"
        );

        self::assertResponseStatusCodeSame(204);

        $deletedCountry = $repository->ofIdDeleted($newCountry->getId());

        $this->assertNotNull($deletedCountry);
        $this->assertInstanceOf(Country::class, $deletedCountry);
        $this->assertEquals('test country', $deletedCountry->getName());
        $this->assertEquals('RU', $deletedCountry->getCode());
        $this->assertNotNull($deletedCountry->getCreatedAt());
        $this->assertNull($deletedCountry->getUpdatedAt());
        $this->assertNotNull($deletedCountry->getDeletedAt());
    }
}
