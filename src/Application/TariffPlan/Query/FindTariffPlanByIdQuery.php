<?php

namespace App\Application\TariffPlan\Query;

use App\Application\Query;
use Symfony\Component\Uid\Uuid;

readonly class FindTariffPlanByIdQuery implements Query
{
    public function __construct(private Uuid $tariffPlanId)
    {
    }

    public function getTariffPlanId(): Uuid
    {
        return $this->tariffPlanId;
    }
}
