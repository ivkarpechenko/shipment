<?php

namespace App\Domain\Directory\Repository;

use App\Domain\Directory\Entity\OkatoOktmo;

interface OkatoOktmoRepositoryInterface
{
    public function create(OkatoOktmo $okato): void;

    public function update(OkatoOktmo $okato): void;

    public function ofOkato(string $okato): ?OkatoOktmo;
}
