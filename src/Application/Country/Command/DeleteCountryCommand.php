<?php

namespace App\Application\Country\Command;

use App\Application\Command;
use Symfony\Component\Uid\Uuid;

readonly class DeleteCountryCommand implements Command
{
    public function __construct(private Uuid $countryId)
    {
    }

    public function getCountryId(): Uuid
    {
        return $this->countryId;
    }
}
