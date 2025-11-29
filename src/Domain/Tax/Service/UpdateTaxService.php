<?php

namespace App\Domain\Tax\Service;

use App\Domain\Tax\Exception\TaxNotFoundException;
use App\Domain\Tax\Repository\TaxRepositoryInterface;
use Symfony\Component\Uid\Uuid;

readonly class UpdateTaxService
{
    public function __construct(public TaxRepositoryInterface $repository)
    {
    }

    public function update(Uuid $taxId, float $value): void
    {
        $tax = $this->repository->ofId($taxId);

        if (is_null($tax)) {
            $tax = $this->repository->ofIdDeleted($taxId);

            if (is_null($tax)) {
                throw new TaxNotFoundException(sprintf('Tax with ID: %s not found', $taxId->toRfc4122()));
            }

            throw new TaxNotFoundException(sprintf('Tax with ID: %s deleted', $taxId->toRfc4122()));
        }

        $tax->changeValue($value);

        $this->repository->update($tax);
    }
}
