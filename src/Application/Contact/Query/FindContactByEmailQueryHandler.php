<?php

namespace App\Application\Contact\Query;

use App\Application\QueryHandler;
use App\Domain\Contact\Entity\Contact;
use App\Domain\Contact\Repository\ContactRepositoryInterface;

readonly class FindContactByEmailQueryHandler implements QueryHandler
{
    public function __construct(public ContactRepositoryInterface $repository)
    {
    }

    public function __invoke(FindContactByEmailQuery $query): ?Contact
    {
        return $this->repository->ofEmail($query->getEmail());
    }
}
