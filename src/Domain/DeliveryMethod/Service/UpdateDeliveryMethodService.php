<?php

declare(strict_types=1);

namespace App\Domain\DeliveryMethod\Service;

use App\Domain\DeliveryMethod\Exception\DeliveryMethodNotFoundException;
use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;

readonly class UpdateDeliveryMethodService
{
    public function __construct(
        public DeliveryMethodRepositoryInterface $deliveryMethodRepository
    ) {
    }

    public function update(string $code, ?string $name, ?bool $isActive): void
    {
        $deliveryMethod = $this->deliveryMethodRepository->ofCode($code);
        if (is_null($deliveryMethod)) {
            throw new DeliveryMethodNotFoundException(sprintf('Delivery method with code %s not found', $code));
        }

        if (!is_null($isActive)) {
            $deliveryMethod->changeIsActive($isActive);
        }

        if (!is_null($name)) {
            $deliveryMethod->changeName($name);
        }

        $this->deliveryMethodRepository->update($deliveryMethod);
    }
}
