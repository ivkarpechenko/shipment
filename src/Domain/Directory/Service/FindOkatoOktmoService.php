<?php

declare(strict_types=1);

namespace App\Domain\Directory\Service;

use App\Domain\Directory\Entity\OkatoOktmo;
use App\Domain\Directory\Repository\OkatoOktmoRepositoryInterface;

class FindOkatoOktmoService
{
    public function __construct(
        private OkatoOktmoRepositoryInterface $repository
    ) {
    }

    public function ofOkato(string $okato): ?OkatoOktmo
    {
        return $this->repository->ofOkato($okato);
    }
}
