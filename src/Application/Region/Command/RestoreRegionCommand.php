<?php

namespace App\Application\Region\Command;

use App\Application\Command;
use Symfony\Component\Uid\Uuid;

readonly class RestoreRegionCommand implements Command
{
    public function __construct(private Uuid $regionId)
    {
    }

    public function getRegionId(): Uuid
    {
        return $this->regionId;
    }
}
