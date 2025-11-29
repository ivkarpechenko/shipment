<?php

namespace App\Application\Shipment\Command;

use App\Application\Address\Command\CreateAddressCommand;
use App\Application\Address\Query\FindAddressByAddressQuery;
use App\Application\CommandBus;
use App\Application\CommandHandler;
use App\Application\Contact\Command\CreateContactCommand;
use App\Application\Contact\Query\FindContactByEmailQuery;
use App\Application\Currency\Query\FindCurrencyByCodeDeactivatedQuery;
use App\Application\Currency\Query\FindCurrencyByCodeQuery;
use App\Application\QueryBus;
use App\Application\Shipment\Command\Dto\PackageDto;
use App\Application\Shipment\Query\FindShipmentByIdQuery;
use App\Domain\Currency\Exception\CurrencyDeactivatedException;
use App\Domain\Currency\Exception\CurrencyNotFoundException;
use App\Domain\Shipment\Entity\Package;
use App\Domain\Shipment\Exception\ShipmentNotFoundException;
use App\Domain\Shipment\Service\UpdateShipmentService;

readonly class UpdateShipmentCommandHandler implements CommandHandler
{
    public function __construct(
        public CommandBus $commandBus,
        public QueryBus $queryBus,
        public UpdateShipmentService $updateShipmentService
    ) {
    }

    public function __invoke(UpdateShipmentCommand $command): void
    {
        $updateShipmentDto = $command->updateShipmentDto;

        $shipment = $this->queryBus->handle(new FindShipmentByIdQuery($command->shipmentId));
        if (is_null($shipment)) {
            throw new ShipmentNotFoundException(sprintf(
                'Shipment with ID %s was not found',
                $command->shipmentId->toRfc4122()
            ));
        }

        if (!is_null($updateShipmentDto->from)) {
            $fromAddress = $this->queryBus->handle(new FindAddressByAddressQuery($updateShipmentDto->from));
            if (is_null($fromAddress)) {
                $this->commandBus->dispatch(new CreateAddressCommand($updateShipmentDto->from));

                /**
                 * If command transport async, return throw
                 */
                $fromAddress = $this->queryBus->handle(new FindAddressByAddressQuery($updateShipmentDto->from));
            }

            $this->updateShipmentService->update($command->shipmentId, from: $fromAddress->getAddress());
        }

        if (!is_null($updateShipmentDto->to)) {
            $toAddress = $this->queryBus->handle(new FindAddressByAddressQuery($updateShipmentDto->to));
            if (is_null($toAddress)) {
                $this->commandBus->dispatch(new CreateAddressCommand($updateShipmentDto->to));

                /**
                 * If command transport async, return throw
                 */
                $toAddress = $this->queryBus->handle(new FindAddressByAddressQuery($updateShipmentDto->to));
            }

            $this->updateShipmentService->update($command->shipmentId, to: $toAddress->getAddress());
        }

        if (!is_null($updateShipmentDto->sender)) {
            $sender = $this->queryBus->handle(new FindContactByEmailQuery(
                $updateShipmentDto->sender->email
            ));

            if (is_null($sender)) {
                $this->commandBus->dispatch(new CreateContactCommand(
                    $updateShipmentDto->sender->email,
                    $updateShipmentDto->sender->name,
                    $updateShipmentDto->sender->phones
                ));

                /**
                 * If command transport async, return throw
                 */
                $sender = $this->queryBus->handle(new FindContactByEmailQuery(
                    $updateShipmentDto->sender->email
                ));
            }

            $this->updateShipmentService->update($command->shipmentId, senderId: $sender->getId());
        }

        if (!is_null($updateShipmentDto->recipient)) {
            $recipient = $this->queryBus->handle(new FindContactByEmailQuery(
                $updateShipmentDto->recipient->email
            ));

            if (is_null($recipient)) {
                $this->commandBus->dispatch(new CreateContactCommand(
                    $updateShipmentDto->recipient->email,
                    $updateShipmentDto->recipient->name,
                    $updateShipmentDto->recipient->phones
                ));

                /**
                 * If command transport async, return throw
                 */
                $recipient = $this->queryBus->handle(new FindContactByEmailQuery(
                    $updateShipmentDto->recipient->email
                ));
            }

            $this->updateShipmentService->update($command->shipmentId, recipientId: $recipient->getId());
        }

        if (!is_null($updateShipmentDto->currencyCode)) {
            $currency = $this->queryBus->handle(new FindCurrencyByCodeQuery($updateShipmentDto->currencyCode));
            if (is_null($currency)) {
                $currency = $this->queryBus->handle(new FindCurrencyByCodeDeactivatedQuery($updateShipmentDto->currencyCode));
                if (!is_null($currency)) {
                    throw new CurrencyDeactivatedException(sprintf(
                        'Currency with code %s deactivated',
                        $updateShipmentDto->currencyCode
                    ));
                }

                throw new CurrencyNotFoundException(sprintf(
                    'Currency with code %s not found',
                    $updateShipmentDto->currencyCode
                ));
            }

            $this->updateShipmentService->update($command->shipmentId, currencyCode: $currency->getCode());
        }

        if (!empty($updateShipmentDto->packages)) {
            $this->updateShipmentService->update(
                $command->shipmentId,
                packages: array_map(function (PackageDto $packageDto) {
                    return new Package(
                        $packageDto->price,
                        $packageDto->width,
                        $packageDto->height,
                        $packageDto->length,
                        $packageDto->weight
                    );
                }, $updateShipmentDto->packages)
            );
        }

        if (!is_null($updateShipmentDto->psd)) {
            $this->updateShipmentService->update($command->shipmentId, psd: $updateShipmentDto->psd);
        }

        if (!is_null($updateShipmentDto->psdStartTime)) {
            $this->updateShipmentService->update($command->shipmentId, psdStartTime: $updateShipmentDto->psdStartTime);
        }

        if (!is_null($updateShipmentDto->psdEndTime)) {
            $this->updateShipmentService->update($command->shipmentId, psdEndTime: $updateShipmentDto->psdEndTime);
        }

        if (!is_null($updateShipmentDto->pickupPointId)) {
            $this->updateShipmentService->update($command->shipmentId, pickupPointId: $updateShipmentDto->pickupPointId);
        }
    }
}
