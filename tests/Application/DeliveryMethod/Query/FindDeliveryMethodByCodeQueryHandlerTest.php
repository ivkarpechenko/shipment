<?php

declare(strict_types=1);

namespace App\Tests\Application\DeliveryMethod\Query;

use App\Application\DeliveryMethod\Query\FindDeliveryMethodByCodeQuery;
use App\Application\DeliveryMethod\Query\FindDeliveryMethodByCodeQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Domain\DeliveryMethod\Entity\DeliveryMethod;
use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;
use App\Tests\Fixture\DeliveryMethod\DeliveryMethodFixture;
use App\Tests\MessageBusTestCase;
use Symfony\Component\Uid\Uuid;

class FindDeliveryMethodByCodeQueryHandlerTest extends MessageBusTestCase
{
    public function testQueryInstanceOf(): void
    {
        $this->assertInstanceOf(
            Query::class,
            new FindDeliveryMethodByCodeQuery('test')
        );

        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindDeliveryMethodByCodeQueryHandler::class)
        );
    }

    public function testFindDeliveryMethodByCodeQueryHandler(): void
    {
        $newDeliveryMethod = DeliveryMethodFixture::getOne('test', 'test');

        $deliveryMethodRepository = $this->getContainer()->get(DeliveryMethodRepositoryInterface::class);
        $deliveryMethodRepository->create($newDeliveryMethod);

        $deliveryMethod = $this->getContainer()->get(FindDeliveryMethodByCodeQueryHandler::class)(
            new FindDeliveryMethodByCodeQuery('test')
        );

        $this->assertNotNull($deliveryMethod);
        $this->assertInstanceOf(DeliveryMethod::class, $deliveryMethod);
        $this->assertEquals('test', $deliveryMethod->getCode());
        $this->assertEquals('test', $deliveryMethod->getName());
        $this->assertTrue($deliveryMethod->isActive());
        $this->assertInstanceOf(Uuid::class, $deliveryMethod->getId());
        $this->assertNotNull($deliveryMethod->getCreatedAt());
        $this->assertNull($deliveryMethod->getUpdatedAt());
    }
}
