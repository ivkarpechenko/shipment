<?php

namespace App\Application\Region\Query;

use App\Application\Query;

readonly class GetRegionsByPaginateQuery implements Query
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
