<?php

namespace App\Tests\Infrastructure\Http\DeliveryService\v1;

use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use App\Tests\Fixture\DeliveryService\DeliveryServiceFixture;
use App\Tests\HttpTestCase;

class FindDeliveryServiceControllerTest extends HttpTestCase
{
    public function testFindById()
    {
        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test service');
        $repository = $this->getContainer()->get(DeliveryServiceRepositoryInterface::class);
        $repository->create($newDeliveryService);

        $this->client->request(
            'GET',
            "/api/v1/delivery-service/find-by-id/{$newDeliveryService->getId()->toRfc4122()}"
        );

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

    public function testFindByCode()
    {
        $newDeliveryService = DeliveryServiceFixture::getOne('test', 'test service');
        $repository = $this->getContainer()->get(DeliveryServiceRepositoryInterface::class);
        $repository->create($newDeliveryService);

        $this->client->request(
            'GET',
            "/api/v1/delivery-service/find-by-code/{$newDeliveryService->getCode()}"
        );

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
}
