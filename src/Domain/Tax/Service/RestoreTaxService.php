<?php

namespace App\Domain\Tax\Service;

use App\Domain\Tax\Exception\TaxNotFoundException;
use App\Domain\Tax\Repository\TaxRepositoryInterface;
use Symfony\Component\Uid\Uuid;

readonly class RestoreTaxService
{
    public function __construct(public TaxRepositoryInterface $repository)
    {
    }

    public function restore(Uuid $taxId): void
    {
        $tax = $this->repository->ofIdDeleted($taxId);

        if (is_null($tax)) {
            throw new TaxNotFoundException(sprintf('Tax with ID: %s not found', $taxId->toRfc4122()));
        }

        $tax->restored();

        $this->repository->restore($tax);
    }
}
