<?php

namespace App\Domain\Shipment\Service;

use App\Domain\Shipment\Exception\ShipmentNotFoundException;
use App\Domain\Shipment\Repository\CalculateRepositoryInterface;
use App\Domain\Shipment\Repository\ShipmentRepositoryInterface;
use Symfony\Component\Uid\Uuid;

readonly class ExpireCalculateService
{
    public function __construct(
        public ShipmentRepositoryInterface $shipmentRepository,
        public CalculateRepositoryInterface $calculateRepository
    ) {
    }

    public function expire(Uuid $shipmentId): void
    {
        $shipment = $this->shipmentRepository->ofId($shipmentId);
        if (is_null($shipment)) {
            throw new ShipmentNotFoundException(sprintf('Shipment with ID %s was not found', $shipmentId->toRfc4122()));
        }

        $calculates = $this->calculateRepository->ofShipmentIdNotExpired($shipmentId);
        foreach ($calculates as $calculate) {
            if (is_null($calculate)) {
                return;
            }

            $calculate->changeExpiredAt(new \DateTime('-1 minute'));

            $this->calculateRepository->update($calculate);
        }
    }
}
