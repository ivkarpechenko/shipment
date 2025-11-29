<?php

namespace App\Domain\TariffPlan\Service;

use App\Domain\TariffPlan\Exception\TariffPlanNotFoundException;
use App\Domain\TariffPlan\Repository\TariffPlanRepositoryInterface;

readonly class UpdateTariffPlanService
{
    public function __construct(public TariffPlanRepositoryInterface $repository)
    {
    }

    public function update(string $deliveryServiceCode, string $deliveryMethodCode, string $code, ?string $name, ?bool $isActive): void
    {
        $tariffPlan = $this->repository->ofCode($deliveryServiceCode, $deliveryMethodCode, $code);
        if (is_null($tariffPlan)) {
            $tariffPlan = $this->repository->ofCodeDeactivated($deliveryServiceCode, $deliveryMethodCode, $code);
            if (is_null($tariffPlan)) {
                throw new TariffPlanNotFoundException(sprintf(
                    'The tariff plan with the code %s for the delivery service %s was not found',
                    $code,
                    $deliveryServiceCode
                ));
            }
        }

        if (!is_null($isActive) && !$tariffPlan->equalsIsActive($isActive)) {
            $tariffPlan->changeIsActive($isActive);
        }

        if (!is_null($name)) {
            $tariffPlan->changeName($name);
        }

        $this->repository->update($tariffPlan);
    }
}
