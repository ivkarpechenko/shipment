<?php

namespace App\Tests\Infrastructure\Http\DeliveryService\v1;

use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\HttpTestCase;

class DeliveryServiceListControllerTest extends HttpTestCase
{
    public function testGetAllRoute()
    {
        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test service');
        $repository = $this->getContainer()->get(DeliveryServiceRepositoryInterface::class);
        $repository->create($newDeliveryService);

        $this->client->request('GET', '/api/v1/delivery-service');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('code', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);

        $this->assertStringContainsString('test', $response);
        $this->assertStringContainsString('test service', $response);
    }

    public function testAllByPaginateRoute()
    {
        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test service');
        $repository = $this->getContainer()->get(DeliveryServiceRepositoryInterface::class);
        $repository->create($newDeliveryService);

        $this->client->request('GET', '/api/v1/delivery-service/paginate?page=0&offset=1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('data', $response);
        $this->assertStringContainsString('total', $response);
        $this->assertStringContainsString('pages', $response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('code', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('isActive', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);

        $this->assertStringContainsString('test', $response);
        $this->assertStringContainsString('test service', $response);
    }
}
