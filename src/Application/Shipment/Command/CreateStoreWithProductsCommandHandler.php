<?php

namespace App\Application\Shipment\Command;

use App\Application\CommandBus;
use App\Application\CommandHandler;
use App\Application\QueryBus;
use App\Application\Shipment\Command\Dto\ProductDto;
use App\Application\Shipment\Command\Dto\StoreDto;
use App\Application\Shipment\Query\FindStoreByIdQuery;
use Symfony\Component\Messenger\Stamp\HandledStamp;

readonly class CreateStoreWithProductsCommandHandler implements CommandHandler
{
    public function __construct(
        public CommandBus $commandBus,
        public QueryBus $queryBus
    ) {
    }

    public function __invoke(CreateStoreWithProductsCommand $command): array
    {
        $products = $command->getProducts();
        if (empty($products)) {
            return $products;
        }

        return array_filter(array_map(function (StoreDto $storeDto) use ($products) {
            $storeProducts = $this->findProductsByStore($products, $storeDto);

            if (empty($storeProducts)) {
                return false;
            }

            $storeId = $this->commandBus
                ->dispatch(new CreateStoreCommand($storeDto, $storeProducts))
                ->last(HandledStamp::class)
                ->getResult();

            $store = $this->queryBus->handle(new FindStoreByIdQuery($storeId));

            if (empty($store)) {
                return false;
            }

            return $store;
        }, $this->getUniqueStores($products)));
    }

    protected function getUniqueStores(array $products): array
    {
        /** @var StoreDto[] $stores */
        $stores = array_map(function (ProductDto $productDto) {
            return $productDto->store;
        }, $products);

        $resultStores = [];
        foreach ($stores as $store) {
            $contains = false;
            foreach ($resultStores as $resultStore) {
                if ($resultStore->psd->format('Y-m-d') == $store->psd->format('Y-m-d')
                    && $resultStore->externalId == $store->externalId
                ) {
                    $contains = true;

                    break;
                }
            }
            if (!$contains) {
                $resultStores[] = $store;
            }
        }

        return $resultStores;
    }

    protected function findProductsByStore(array $products, StoreDto $storeDto): array
    {
        return array_filter($products, function (ProductDto $productDto) use ($storeDto) {
            return $productDto->store->externalId === $storeDto->externalId
                && $productDto->store->psd->format('Y-m-d') == $storeDto->psd->format('Y-m-d');
        });
    }
}
