<?php

declare(strict_types=1);

namespace App\Application;

interface QueryBus
{
    public function handle(Query $query): mixed;
}
