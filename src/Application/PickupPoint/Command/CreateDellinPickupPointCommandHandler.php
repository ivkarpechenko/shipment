<?php

declare(strict_types=1);

namespace App\Application\PickupPoint\Command;

use App\Application\CommandBus;
use App\Application\CommandHandler;
use App\Application\DeliveryService\Query\FindDeliveryServiceByCodeQuery;
use App\Application\QueryBus;
use App\Domain\Address\ValueObject\Point;
use App\Domain\DeliveryService\Enum\DeliveryServiceEnum;
use App\Domain\DeliveryService\Exception\DeliveryServiceNotFoundException;
use App\Domain\PickupPoint\Service\ChangePickupPointService;
use App\Domain\PickupPoint\Service\Dto\PickupPointDto;

readonly class CreateDellinPickupPointCommandHandler implements CommandHandler
{
    public function __construct(
        public ChangePickupPointService $changePickupPointService,
        public CommandBus $commandBus,
        public QueryBus $queryBus
    ) {
    }

    public function __invoke(CreateDellinPickupPointCommand $command): void
    {
        $deliveryService = $this->queryBus->handle(new FindDeliveryServiceByCodeQuery(DeliveryServiceEnum::DELLIN->value));

        if (is_null($deliveryService)) {
            throw new DeliveryServiceNotFoundException(sprintf('Delivery service with code %s not found', DeliveryServiceEnum::DELLIN->value));
        }

        $point = null;
        if (!is_null($command->dto->latitude) && !is_null($command->dto->longitude)) {
            $point = new Point($command->dto->latitude, $command->dto->longitude);
        }

        $pickupPointDto = new PickupPointDto(
            name: $command->dto->name,
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
