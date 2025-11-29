<?php

namespace App\Application\Contact\Query;

use App\Application\Query;
use Symfony\Component\Uid\Uuid;

readonly class FindContactByIdQuery implements Query
{
    public function __construct(private Uuid $contactId)
    {
    }

    public function getContactId(): Uuid
    {
        return $this->contactId;
    }
}
