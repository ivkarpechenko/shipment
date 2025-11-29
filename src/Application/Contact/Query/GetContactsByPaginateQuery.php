<?php

namespace App\Application\Contact\Query;

use App\Application\Query;

readonly class GetContactsByPaginateQuery implements Query
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
