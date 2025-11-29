<?php

namespace App\Domain\Tax\Service;

use App\Domain\Tax\Exception\TaxNotFoundException;
use App\Domain\Tax\Repository\TaxRepositoryInterface;
use Symfony\Component\Uid\Uuid;

readonly class DeleteTaxService
{
    public function __construct(public TaxRepositoryInterface $repository)
    {
    }

    public function delete(Uuid $taxId): void
    {
        $tax = $this->repository->ofId($taxId);

        if (is_null($tax)) {
            throw new TaxNotFoundException(sprintf('Tax with ID: %s not found', $taxId->toRfc4122()));
        }

        $tax->deleted();

        $this->repository->delete($tax);
    }
}
