<?php

namespace App\Application\TariffPlan\Query;

use App\Application\Query;

readonly class GetTariffPlansByPaginateQuery implements Query
{
    public function __construct(private int $page, private int $offset)
    {
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }
}
