<?php

namespace App\Application\Contact\Query;

use App\Application\QueryHandler;
use App\Domain\Contact\Repository\ContactRepositoryInterface;

readonly class GetContactsByPaginateQueryHandler implements QueryHandler
{
    public function __construct(public ContactRepositoryInterface $repository)
    {
    }

    public function __invoke(GetContactsByPaginateQuery $query): array
    {
        return $this->repository->paginate($query->getPage(), $query->getOffset());
    }
}
