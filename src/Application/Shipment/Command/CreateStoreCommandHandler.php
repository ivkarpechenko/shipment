<?php

namespace App\Application\Shipment\Command;

use App\Application\Address\Command\CreateAddressCommand;
use App\Application\Address\Query\FindAddressByAddressQuery;
use App\Application\CommandBus;
use App\Application\CommandHandler;
use App\Application\Contact\Command\CreateContactCommand;
use App\Application\Contact\Query\FindContactByEmailQuery;
use App\Application\QueryBus;
use App\Domain\Address\Entity\Address;
use App\Domain\Contact\Entity\Contact;
use App\Domain\Shipment\Service\CreateStoreService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Uid\Uuid;

readonly class CreateStoreCommandHandler implements CommandHandler
{
    public function __construct(
        public CommandBus $commandBus,
        public QueryBus $queryBus,
        public CreateStoreService $addStoreService,
        public LoggerInterface $logger
    ) {
    }

    public function __invoke(CreateStoreCommand $command): ?Uuid
    {
        $storeDto = $command->getStoreDto();

        /** @var Contact $sender */
        $sender = $this->queryBus->handle(new FindContactByEmailQuery($storeDto->contact->email));
        if (is_null($sender)) {
            try {
                $this->commandBus->dispatch(new CreateContactCommand(
                    $storeDto->contact->email,
                    $storeDto->contact->name,
                    $storeDto->contact->phones
                ));

                /**
                 * If command transport async, return throw
                 */
                $sender = $this->queryBus->handle(new FindContactByEmailQuery($storeDto->contact->email));

                if (is_null($sender)) {
                    throw new \Exception(sprintf(
                        'Could not find store contact information: email %s, externalId: %d',
                        $storeDto->contact->email,
                        $storeDto->externalId
                    ));
                }
            } catch (\Throwable $exception) {
                $this->logger->critical(sprintf(
                    'Failed to create store contact information: email %s, externalId: %d, exception: %s',
                    $storeDto->contact->email,
                    $storeDto->externalId,
                    $exception->getMessage()
                ));

                return null;
            }
        }

        /** @var Address $fromAddress */
        $fromAddress = $this->queryBus->handle(new FindAddressByAddressQuery($storeDto->address));
        if (is_null($fromAddress)) {
            try {
                $this->commandBus->dispatch(new CreateAddressCommand($storeDto->address));

                /**
                 * If command transport async, return throw
                 */
                $fromAddress = $this->queryBus->handle(new FindAddressByAddressQuery($storeDto->address));

                if (is_null($fromAddress)) {
                    throw new \Exception(sprintf(
                        'Could not find store address: %s',
                        $storeDto->address
                    ));
                }
            } catch (\Throwable $exception) {
                $this->logger->critical(sprintf(
                    'Failed to create store address: address %s, externalId: %d, exception: %s',
                    $storeDto->address,
                    $storeDto->externalId,
                    $exception->getMessage()
                ));

                return null;
            }
        }

        return $this->addStoreService->create(
            $sender,
            $fromAddress,
            $storeDto->externalId,
            $storeDto->maxWeight,
            $storeDto->maxVolume,
            $storeDto->maxLength,
            $storeDto->isPickup,
            $storeDto->psd,
            $storeDto->psdStartTime,
            $storeDto->psdEndTime,
            $storeDto->schedules,
            $command->getProducts()
        );
    }
}
