<?php

declare(strict_types=1);

namespace App\Tests\Application\DeliveryMethod\Query;

use App\Application\DeliveryMethod\Query\GetAllDeliveryMethodQuery;
use App\Application\DeliveryMethod\Query\GetAllDeliveryMethodQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;
use App\Tests\Fixture\DeliveryMethod\DeliveryMethodFixture;
use App\Tests\MessageBusTestCase;

class GetAllDeliveryMethodQueryHandlerTest extends MessageBusTestCase
{
    public function testQueryInstanceOf(): void
    {
        $this->assertInstanceOf(
            Query::class,
            new GetAllDeliveryMethodQuery()
        );

        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(GetAllDeliveryMethodQueryHandler::class)
        );
    }

    public function testFindAllDeliveryMethodQueryHandler(): void
    {
        $deliveryMethodRepository = $this->getContainer()->get(DeliveryMethodRepositoryInterface::class);
        $this->assertEmpty($deliveryMethodRepository->all());

        $firstDeliveryMethod = DeliveryMethodFixture::getOne('test 1', 'test 1');
        $deliveryMethodRepository->create($firstDeliveryMethod);
        $secondDeliveryMethod = DeliveryMethodFixture::getOne('test 2', 'test 2');
        $deliveryMethodRepository->create($secondDeliveryMethod);

        $deliveryMethods = $this->getContainer()->get(GetAllDeliveryMethodQueryHandler::class)(
            new GetAllDeliveryMethodQuery()
        );
        $this->assertCount(2, $deliveryMethods);

        $deliveryMethod = $deliveryMethodRepository->ofId($firstDeliveryMethod->getId());
        $deliveryMethod->deactivate();
        $deliveryMethodRepository->update($deliveryMethod);

        $activeDeliveryMethods = $this->getContainer()->get(GetAllDeliveryMethodQueryHandler::class)(
            new GetAllDeliveryMethodQuery(true)
        );
        $this->assertCount(1, $activeDeliveryMethods);

        $deactivatedDeliveryMethods = $this->getContainer()->get(GetAllDeliveryMethodQueryHandler::class)(
            new GetAllDeliveryMethodQuery(false)
        );
        $this->assertCount(1, $deactivatedDeliveryMethods);
    }
}
