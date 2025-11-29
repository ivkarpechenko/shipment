<?php

namespace App\Application\Shipment\Command;

use App\Application\Address\Command\CreateAddressCommand;
use App\Application\Address\Query\External\FindExternalAddressInterface;
use App\Application\Address\Query\FindAddressByAddressQuery;
use App\Application\CommandBus;
use App\Application\CommandHandler;
use App\Application\Contact\Command\CreateContactCommand;
use App\Application\Contact\Query\FindContactByEmailQuery;
use App\Application\Currency\Query\FindCurrencyByCodeDeactivatedQuery;
use App\Application\Currency\Query\FindCurrencyByCodeQuery;
use App\Application\QueryBus;
use App\Application\Shipment\Query\FindProductByStoreAndDeliveryPeriodQuery;
use App\Application\Shipment\Query\FindShipmentByIdQuery;
use App\Domain\Address\Entity\Address;
use App\Domain\Currency\Exception\CurrencyDeactivatedException;
use App\Domain\Currency\Exception\CurrencyNotFoundException;
use App\Domain\Directory\Exception\OkatoNotFoundException;
use App\Domain\Directory\Exception\OktmoNotFoundException;
use App\Domain\Directory\Service\FindOkatoOktmoService;
use App\Domain\Shipment\Exception\ShipmentNotCreatedException;
use App\Domain\Shipment\Exception\StoreProductNotFoundException;
use App\Domain\Shipment\Service\CreateCargoRestrictionService;
use App\Domain\Shipment\Service\CreateComplexStoresService;
use App\Domain\Shipment\Service\CreatePackageService;
use App\Domain\Shipment\Service\CreateShipmentService;
use App\Infrastructure\DaData\Service\FindAddressByOktmoService;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

readonly class TekTorgCreateShipmentCommandHandler implements CommandHandler
{
    public function __construct(
        public CommandBus $commandBus,
        public QueryBus $queryBus,
        public CreateShipmentService $createShipmentService,
        public CreatePackageService $createPackageService,
        public CreateComplexStoresService $createComplexStoresService,
        public CreateCargoRestrictionService $createCargoRestrictionService,
        public FindAddressByOktmoService $findAddressByOktmoService,
        public FindExternalAddressInterface $findExternalAddress,
        public FindOkatoOktmoService $findOkatoOktmoService,
        public LoggerInterface $logger,
        public ParameterBagInterface $parameterBag
    ) {
    }

    public function __invoke(TekTorgCreateShipmentCommand $command): array
    {
        $createShipmentDto = $command->getBulkCreateShipmentDto();

        if (!is_null($createShipmentDto->oktmo)) {
            try {
                // Получаем почтовый адрес из DaData
                $daDataOktmoDto = $this->findAddressByOktmoService->find($createShipmentDto->oktmo);

                $area = $daDataOktmoDto->area;
                $subarea = $daDataOktmoDto->subarea;

                $postAddress = trim(implode(', ', array_filter([$area, $subarea])));
            } catch (\Exception) {
                throw new OktmoNotFoundException('ОКТМО не найден.');
            }
        } else {
            $okatoOktmoEntity = $this->findOkatoOktmoService->ofOkato($createShipmentDto->okato);
            if (!is_null($okatoOktmoEntity)) {
                try {
                    // Получаем почтовый адрес из DaData
                    $daDataOktmoDto = $this->findAddressByOktmoService->find($okatoOktmoEntity->getOktmo());

                    $area = $daDataOktmoDto->area;
                    $subarea = $daDataOktmoDto->subarea;

                    $postAddress = trim(implode(', ', array_filter([$area, $subarea])));
                } catch (\Exception) {
                    throw new OkatoNotFoundException('ОКАТО не найден.');
                }
            } else {
                throw new OkatoNotFoundException('ОКАТО не найден.');
            }
        }

        // Выполняем запрос в DaData suggest, чтобы получить полную информацию
        $daDataAddress = $this->findExternalAddress->find($postAddress);
        /** @var Address $toAddress */
        $toAddress = $this->queryBus->handle(new FindAddressByAddressQuery($daDataAddress->address));

        if (is_null($toAddress)) {
            $this->commandBus->dispatch(new CreateAddressCommand($daDataAddress->address));
            $toAddress = $this->queryBus->handle(new FindAddressByAddressQuery($daDataAddress->address));
        }

        $recipient = $this->queryBus->handle(new FindContactByEmailQuery($createShipmentDto->recipient->email));
        if (is_null($recipient)) {
            $this->commandBus->dispatch(
                new CreateContactCommand(
                    $createShipmentDto->recipient->email,
                    $createShipmentDto->recipient->name,
                    $createShipmentDto->recipient->phones
                )
            );

            /**
             * If command transport async, return throw
             */
            $recipient = $this->queryBus->handle(new FindContactByEmailQuery($createShipmentDto->recipient->email));
        }

        $currency = $this->queryBus->handle(new FindCurrencyByCodeQuery($createShipmentDto->currencyCode));
        if (is_null($currency)) {
            $currency = $this->queryBus->handle(
                new FindCurrencyByCodeDeactivatedQuery($createShipmentDto->currencyCode)
            );
            if (!is_null($currency)) {
                $this->logger->critical(
                    sprintf(
                        'Currency with code %s deactivated',
                        $createShipmentDto->currencyCode
                    )
                );

                throw new CurrencyDeactivatedException(
                    sprintf(
                        'Currency with code %s deactivated',
                        $createShipmentDto->currencyCode
                    )
                );
            }

            $this->logger->critical(
                sprintf(
                    'Currency with code %s not found',
                    $createShipmentDto->currencyCode
                )
            );

            throw new CurrencyNotFoundException(
                sprintf('Currency with code %s not found', $createShipmentDto->currencyCode)
            );
        }

        // Создание складов
        $stores = $this->commandBus
            ->dispatch(new CreateStoreWithProductsCommand($createShipmentDto->products))
            ->last(HandledStamp::class)
            ->getResult();

        if (empty($stores)) {
            $this->logger->critical(
                sprintf(
                    'Error when creating stores with products, recipient address %s',
                    $toAddress?->getAddress() ?? '-'
                )
            );

            throw new StoreProductNotFoundException(
                sprintf(
                    'Error when creating stores with products, recipient address %s',
                    $toAddress?->getAddress() ?? '-'
                )
            );
        }

        $stores = $this->createComplexStoresService->create($stores);

        $shipments = [];

        foreach ($stores as $key => $store) {
            [$externalId, $deliveryPeriod] = explode(':', $key);

            try {
                $storeProducts = $this->queryBus->handle(
                    new FindProductByStoreAndDeliveryPeriodQuery(
                        $store->getId(),
                        $deliveryPeriod
                    )
                );
                if (empty($storeProducts)) {
                    $this->logger->critical(
                        sprintf(
                            'Store products not found when creating shipment for  storeUUID: %s, externalId: %d',
                            $store->getId()->toRfc4122(),
                            $store->getExternalId()
                        )
                    );

                    continue;
                }

                $packages = $this->createPackageService->create(
                    $storeProducts,
                    $this->parameterBag->get('package.max_weight'),
                    $this->parameterBag->get('package.max_height'),
                    $this->parameterBag->get('package.max_width'),
                    $this->parameterBag->get('package.max_length')
                );
                if (empty($packages)) {
                    $this->logger->critical(
                        sprintf(
                            'Failed to calculate packages when creating shipment for storeUUID: %s, externalId: %d',
                            $store->getId()->toRfc4122(),
                            $store->getExternalId()
                        )
                    );

                    continue;
                }

                $shipmentId = $this->createShipmentService->create(
                    $store->getAddress()->getAddress(),
                    $toAddress->getAddress(),
                    $store->getContact()->getId(),
                    $recipient->getId(),
                    $currency->getCode(),
                    $packages,
                    // TODO refactoring
                    $store->getPsd(),
                    $store->getPsdStartTime(),
                    $store->getPsdEndTime()
                );

                foreach ($command->getBulkCreateShipmentDto()->cargoRestrictions as $cargoRestriction) {
                    $this->createCargoRestrictionService->create(
                        $shipmentId,
                        $cargoRestriction->code,
                        $cargoRestriction->maxWidth,
                        $cargoRestriction->maxHeight,
                        $cargoRestriction->maxLength,
                        $cargoRestriction->maxWeight,
                        $cargoRestriction->maxVolume,
                        $cargoRestriction->maxSumDimensions
                    );
                }

                $shipment = $this->queryBus->handle(new FindShipmentByIdQuery($shipmentId));

                if (empty($shipment)) {
                    continue;
                }

                $shipments[] = $shipment;
            } catch (\Throwable $exception) {
                $this->logger->critical(
                    sprintf(
                        'Failed to create shipment for storeUUID: %s, externalId: %d, exception: %s',
                        $store->getId()->toRfc4122(),
                        $store->getExternalId(),
                        $exception->getMessage()
                    )
                );

                throw new ShipmentNotCreatedException('Shipment not created exception');
            }
        }

        return $shipments;
    }
}
