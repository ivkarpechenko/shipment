<?php

namespace App\Domain\TariffPlan\Repository;

use App\Domain\TariffPlan\Entity\TariffPlan;
use Symfony\Component\Uid\Uuid;

interface TariffPlanRepositoryInterface
{
    public function create(TariffPlan $tariffPlan): void;

    public function update(TariffPlan $tariffPlan): void;

    public function ofId(Uuid $tariffPlanId): ?TariffPlan;

    public function ofIdDeactivated(Uuid $tariffPlanId): ?TariffPlan;

    public function ofCode(string $deliveryServiceCode, string $deliveryMethodCode, string $code): ?TariffPlan;

    public function ofCodeDeactivated(string $deliveryServiceCode, string $deliveryMethodCode, string $code): ?TariffPlan;

    public function all(): array;

    public function paginate(int $page, int $offset): array;

    public function active(): array;

    /**
     * @return TariffPlan[]
     */
    public function ofServiceAndMethod(string $deliveryServiceCode, string $deliveryMethodCode, ?bool $isActive = null): array;
}
