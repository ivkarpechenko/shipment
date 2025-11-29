<?php

namespace App\Domain\Shipment\Service;

use App\Domain\Address\Entity\Address;
use App\Domain\Contact\Entity\Contact;
use App\Domain\Shipment\Entity\Product;
use App\Domain\Shipment\Entity\Store;
use App\Domain\Shipment\Entity\StoreSchedule;
use App\Domain\Shipment\Repository\StoreRepositoryInterface;
use App\Domain\Shipment\ValueObject\Day;
use App\Domain\Shipment\ValueObject\EndTime;
use App\Domain\Shipment\ValueObject\StartTime;
use Symfony\Component\Uid\Uuid;

readonly class CreateStoreService
{
    public function __construct(public StoreRepositoryInterface $storeRepository)
    {
    }

    public function create(
        Contact $contact,
        Address $address,
        int $externalId,
        int $maxWeight,
        int $maxVolume,
        int $maxLength,
        bool $isPickup = false,
        ?\DateTime $psd = null,
        ?\DateTime $psdStartTime = null,
        ?\DateTime $psdEndTime = null,
        array $schedules = [],
        array $products = []
    ): Uuid {
        $store = new Store(
            $contact,
            $address,
            $externalId,
            $maxWeight,
            $maxVolume,
            $maxLength,
            $isPickup,
            $psd,
            $psdStartTime,
            $psdEndTime
        );

        foreach ($schedules as $schedule) {
            $store->addSchedule(new StoreSchedule(
                new Day($schedule->day),
                new StartTime($schedule->startTime),
                new EndTime($schedule->endTime)
            ));
        }

        foreach ($products as $product) {
            $store->addProduct(new Product(
                $product->code,
                $product->description,
                $product->price,
                $product->weight,
                $product->width,
                $product->height,
                $product->length,
                $product->quantity,
                $product->isFragile,
                $product->isFlammable,
                $product->isCanRotate,
                $product->deliveryPeriod
            ));
        }

        return $this->storeRepository->create($store);
    }
}
