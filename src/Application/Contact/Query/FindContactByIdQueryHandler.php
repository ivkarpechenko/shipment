<?php

namespace App\Application\Contact\Query;

use App\Application\QueryHandler;
use App\Domain\Contact\Entity\Contact;
use App\Domain\Contact\Repository\ContactRepositoryInterface;

class FindContactByIdQueryHandler implements QueryHandler
{
    public function __construct(public ContactRepositoryInterface $repository)
    {
    }

    public function __invoke(FindContactByIdQuery $query): ?Contact
    {
        return $this->repository->ofId($query->getContactId());
    }
}
