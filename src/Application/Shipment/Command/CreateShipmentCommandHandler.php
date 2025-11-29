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
use App\Domain\Currency\Exception\CurrencyDeactivatedException;
use App\Domain\Currency\Exception\CurrencyNotFoundException;
use App\Domain\Shipment\Entity\Package;
use App\Domain\Shipment\Entity\PackageProduct;
use App\Domain\Shipment\Entity\Product;
use App\Domain\Shipment\Service\CreateShipmentService;
use Symfony\Component\Uid\Uuid;

readonly class CreateShipmentCommandHandler implements CommandHandler
{
    public function __construct(
        public CommandBus $commandBus,
        public QueryBus $queryBus,
        public CreateShipmentService $createShipmentService
    ) {
    }

    public function __invoke(CreateShipmentCommand $command): Uuid
    {
        $createShipmentDto = $command->createShipmentDto;

        $fromAddress = $this->queryBus->handle(new FindAddressByAddressQuery($createShipmentDto->from));
        if (is_null($fromAddress)) {
            $this->commandBus->dispatch(new CreateAddressCommand($createShipmentDto->from));

            /**
             * If command transport async, return throw
             */
            $fromAddress = $this->queryBus->handle(new FindAddressByAddressQuery($createShipmentDto->from));
        }

        $toAddress = $this->queryBus->handle(new FindAddressByAddressQuery($createShipmentDto->to));
        if (is_null($toAddress)) {
            $this->commandBus->dispatch(new CreateAddressCommand($createShipmentDto->to));

            /**
             * If command transport async, return throw
             */
            $toAddress = $this->queryBus->handle(new FindAddressByAddressQuery($createShipmentDto->to));
        }

        $sender = $this->queryBus->handle(new FindContactByEmailQuery($createShipmentDto->sender->email));
        if (is_null($sender)) {
            $this->commandBus->dispatch(new CreateContactCommand(
                $createShipmentDto->sender->email,
                $createShipmentDto->sender->name,
                $createShipmentDto->sender->phones
            ));

            /**
             * If command transport async, return throw
             */
            $sender = $this->queryBus->handle(new FindContactByEmailQuery($createShipmentDto->sender->email));
        }

        $recipient = $this->queryBus->handle(new FindContactByEmailQuery($createShipmentDto->recipient->email));
        if (is_null($recipient)) {
            $this->commandBus->dispatch(new CreateContactCommand(
                $createShipmentDto->recipient->email,
                $createShipmentDto->recipient->name,
                $createShipmentDto->recipient->phones
            ));

            /**
             * If command transport async, return throw
             */
            $recipient = $this->queryBus->handle(new FindContactByEmailQuery($createShipmentDto->recipient->email));
        }

        $currency = $this->queryBus->handle(new FindCurrencyByCodeQuery($createShipmentDto->currencyCode));
        if (is_null($currency)) {
            $currency = $this->queryBus->handle(new FindCurrencyByCodeDeactivatedQuery($createShipmentDto->currencyCode));
            if (!is_null($currency)) {
                throw new CurrencyDeactivatedException(sprintf(
                    'Currency with code %s deactivated',
                    $createShipmentDto->currencyCode
                ));
            }

            throw new CurrencyNotFoundException(sprintf('Currency with code %s not found', $createShipmentDto->currencyCode));
        }

        return $this->createShipmentService->create(
            $fromAddress->getAddress(),
            $toAddress->getAddress(),
            $sender->getId(),
            $recipient->getId(),
            $currency->getCode(),
            array_map(function (PackageDto $packageDto) {
                $package = new Package(
                    $packageDto->price,
                    $packageDto->width,
                    $packageDto->height,
                    $packageDto->length,
                    $packageDto->weight
                );

                foreach ($packageDto->products as $productDto) {
                    $packageProduct = new PackageProduct(1);
                    $packageProduct->setProduct(new Product(
                        $productDto->code,
                        $productDto->description,
                        $productDto->price,
                        0,
                        0,
                        0,
                        0,
                        $productDto->quantity,
                        false,
                        false,
                        false,
                        0
                    ));

                    $package->addProduct($packageProduct);
                }

                return $package;
            }, $createShipmentDto->packages),
            $createShipmentDto->psd,
            $createShipmentDto->psdStartTime,
            $createShipmentDto->psdEndTime
        );
    }
}
