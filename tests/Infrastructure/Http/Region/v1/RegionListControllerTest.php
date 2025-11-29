<?php

namespace App\Tests\Infrastructure\Http\Region\v1;

use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\Region\RegionFixture;
use App\Tests\HttpTestCase;

class RegionListControllerTest extends HttpTestCase
{
    public function testAll()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $newRegion = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $repository = $this->getContainer()->get(RegionRepositoryInterface::class);
        $repository->create($newRegion);

        $this->client->request('GET', '/api/v1/region');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('country', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('code', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);
        $this->assertStringContainsString('deletedAt', $response);

        $this->assertStringContainsString('RU', $response);
        $this->assertStringContainsString('Russia', $response);
        $this->assertStringContainsString('Moskva', $response);
        $this->assertStringContainsString('MOW', $response);
    }

    public function testPaginate()
    {
        $container = $this->getContainer();

        $country = CountryFixture::getOne('Russia', 'RU');

        $repositoryCountry = $container->get(CountryRepositoryInterface::class);
        $repositoryCountry->create($country);

        $country = $repositoryCountry->ofId($country->getId());

        $newRegion = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $repository = $this->getContainer()->get(RegionRepositoryInterface::class);
        $repository->create($newRegion);

        $this->client->request(
            'GET',
            '/api/v1/region/paginate?page=0&offset=1'
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('country', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('code', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);
        $this->assertStringContainsString('deletedAt', $response);
        $this->assertStringContainsString('total', $response);
        $this->assertStringContainsString('pages', $response);

        $this->assertStringContainsString('RU', $response);
        $this->assertStringContainsString('Russia', $response);
        $this->assertStringContainsString('Moskva', $response);
        $this->assertStringContainsString('MOW', $response);
    }
}
