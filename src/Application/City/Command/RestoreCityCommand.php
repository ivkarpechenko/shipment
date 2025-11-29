<?php

namespace App\Application\City\Command;

use App\Application\Command;
use Symfony\Component\Uid\Uuid;

readonly class RestoreCityCommand implements Command
{
    public function __construct(private Uuid $cityId)
    {
    }

    public function getCityId(): Uuid
    {
        return $this->cityId;
    }
}
