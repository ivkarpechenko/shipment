<?php

declare(strict_types=1);

namespace App\Domain\Directory\Service;

use App\Domain\Directory\Dto\OkatoOktmoDto;
use App\Domain\Directory\Entity\OkatoOktmo;
use App\Domain\Directory\Repository\OkatoOktmoRepositoryInterface;

readonly class CreateOkatoOktmoService
{
    public function __construct(
        private OkatoOktmoRepositoryInterface $okatoRepository
    ) {
    }

    public function create(OkatoOktmoDto $dto): void
    {
        if ($this->okatoRepository->ofOkato($dto->okato)) {
            return;
        }

        $okato = new OkatoOktmo(
            $dto->okato,
            $dto->oktmo,
            $dto->location
        );

        $this->okatoRepository->create($okato);
    }
}
