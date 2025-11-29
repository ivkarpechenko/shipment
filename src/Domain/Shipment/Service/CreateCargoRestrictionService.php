<?php

declare(strict_types=1);

namespace App\Domain\Shipment\Service;

use App\Domain\Shipment\Entity\CargoRestriction;
use App\Domain\Shipment\Exception\CargoTypeDeactivatedException;
use App\Domain\Shipment\Exception\CargoTypeNotFoundException;
use App\Domain\Shipment\Exception\ShipmentNotFoundException;
use App\Domain\Shipment\Repository\CargoRestrictionRepositoryInterface;
use App\Domain\Shipment\Repository\CargoTypeRepositoryInterface;
use App\Domain\Shipment\Repository\ShipmentRepositoryInterface;
use Symfony\Component\Uid\Uuid;

readonly class CreateCargoRestrictionService
{
    public function __construct(
        public CargoRestrictionRepositoryInterface $cargoRestrictionRepository,
        public CargoTypeRepositoryInterface $cargoTypeRepository,
        public ShipmentRepositoryInterface $shipmentRepository
    ) {
    }

    public function create(
        Uuid $shipmentId,
        string $cargoTypeCode,
        int $maxWidth,
        int $maxHeight,
        int $maxLength,
        int $maxWeight,
        int $maxVolume,
        int $maxSumDimensions
    ): void {
        $shipment = $this->shipmentRepository->ofId($shipmentId);
        if (is_null($shipment)) {
            throw new ShipmentNotFoundException(sprintf('Shipment with ID %s not found', $shipmentId));
        }

        $cargoType = $this->cargoTypeRepository->ofCode($cargoTypeCode);
        if (is_null($cargoType)) {
            $cargoType = $this->cargoTypeRepository->ofCodeDeactivated($cargoTypeCode);
            if (!is_null($cargoType)) {
                throw new CargoTypeDeactivatedException(sprintf('Cargo type with code "%s" deactivated', $cargoTypeCode));
            }

            throw new CargoTypeNotFoundException(sprintf('Cargo type with code "%s" not found', $cargoTypeCode));
        }

        $cargoRestriction = new CargoRestriction(
            cargoType: $cargoType,
            shipment: $shipment,
            maxWidth: $maxWidth,
            maxHeight: $maxHeight,
            maxLength: $maxLength,
            maxWeight: $maxWeight,
            maxVolume: $maxVolume,
            maxSumDimensions: $maxSumDimensions
        );

        $this->cargoRestrictionRepository->create($cargoRestriction);
    }
}
