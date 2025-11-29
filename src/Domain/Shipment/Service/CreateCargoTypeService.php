<?php

declare(strict_types=1);

namespace App\Domain\Shipment\Service;

use App\Domain\Shipment\Entity\CargoType;
use App\Domain\Shipment\Exception\CargoTypeAlreadyCreatedException;
use App\Domain\Shipment\Exception\CargoTypeDeactivatedException;
use App\Domain\Shipment\Repository\CargoTypeRepositoryInterface;

readonly class CreateCargoTypeService
{
    public function __construct(
        public CargoTypeRepositoryInterface $cargoTypeRepository
    ) {
    }

    public function create(string $code, string $name): void
    {
        $cargoType = $this->cargoTypeRepository->ofCode($code);
        if (!is_null($cargoType)) {
            throw new CargoTypeAlreadyCreatedException(sprintf('Cargo type with code "%s" already created', $code));
        }

        $cargoType = $this->cargoTypeRepository->ofCodeDeactivated($code);
        if (!is_null($cargoType)) {
            throw new CargoTypeDeactivatedException(sprintf('Cargo type with code "%s" deactivated', $code));
        }

        $cargoType = new CargoType($code, $name);

        $this->cargoTypeRepository->create($cargoType);
    }
}
