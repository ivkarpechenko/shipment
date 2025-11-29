<?php

namespace App\Application\PickupPoint\Command;

use App\Application\CommandBus;
use App\Application\CommandHandler;
use App\Application\DeliveryService\Query\FindDeliveryServiceByCodeQuery;
use App\Application\QueryBus;
use App\Domain\Address\ValueObject\Point;
use App\Domain\DeliveryService\Exception\DeliveryServiceNotFoundException;
use App\Domain\PickupPoint\Service\ChangePickupPointService;
use App\Domain\PickupPoint\Service\Dto\PickupPointDto;

readonly class CreateCdekPickupPointCommandHandler implements CommandHandler
{
    public function __construct(
        public ChangePickupPointService $changePickupPointService,
        public CommandBus $commandBus,
        public QueryBus $queryBus
    ) {
    }

    public function __invoke(CreateCdekPickupPointCommand $command): void
    {
        $deliveryService = $this->queryBus->handle(new FindDeliveryServiceByCodeQuery('cdek'));

        if (is_null($deliveryService)) {
            throw new DeliveryServiceNotFoundException('Delivery service with code cdek not found');
        }

        $point = null;
        if (!is_null($command->dto->latitude) && !is_null($command->dto->longitude)) {
            $point = new Point($command->dto->latitude, $command->dto->longitude);
        }

        $pickupPointDto = new PickupPointDto(
            name: $command->dto->code,
            deliveryService: $deliveryService,
            phones: $command->dto->phones,
            point: $point,
            address: $command->dto->address,
            workTime: $command->dto->workTime,
            code: $command->dto->code,
            type: $command->dto->type,
            weightMin: $command->dto->weightMin,
            weightMax: $command->dto->weightMax,
            width: $command->dto->width,
            height: $command->dto->height,
            depth: $command->dto->depth,
        );

        $this->commandBus->dispatch(new CreatePickupPointCommand($pickupPointDto));
    }
}
