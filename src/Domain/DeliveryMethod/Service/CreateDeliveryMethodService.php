<?php

declare(strict_types=1);

namespace App\Domain\DeliveryMethod\Service;

use App\Domain\DeliveryMethod\Entity\DeliveryMethod;
use App\Domain\DeliveryMethod\Exception\DeliveryMethodAlreadyCreatedException;
use App\Domain\DeliveryMethod\Exception\DeliveryMethodDeactivatedException;
use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;

readonly class CreateDeliveryMethodService
{
    public function __construct(
        public DeliveryMethodRepositoryInterface $deliveryMethodRepository
    ) {
    }

    /**
     * @throws DeliveryMethodAlreadyCreatedException
     * @throws DeliveryMethodDeactivatedException
     */
    public function create(string $code, string $name): void
    {
        $deliveryMethod = $this->deliveryMethodRepository->ofCode($code);
        if (!is_null($deliveryMethod)) {
            throw new DeliveryMethodAlreadyCreatedException(sprintf('Delivery method with code %s already created', $code));
        }

        $deliveryMethod = $this->deliveryMethodRepository->ofCodeDeactivated($code);
        if (!is_null($deliveryMethod)) {
            throw new DeliveryMethodDeactivatedException(sprintf('Delivery method with code %s deactivated', $code));
        }

        $deliveryMethod = new DeliveryMethod($code, $name);
        $this->deliveryMethodRepository->create($deliveryMethod);
    }
}
