<?php

namespace App\Application\DeliveryService\Query;

use App\Application\Query;

readonly class GetDeliveryServicesByPaginateQuery implements Query
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
