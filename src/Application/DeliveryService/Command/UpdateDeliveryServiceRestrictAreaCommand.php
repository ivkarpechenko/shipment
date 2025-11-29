<?php

namespace App\Application\DeliveryService\Command;

use App\Application\Command;
use App\Domain\DeliveryService\ValueObject\Polygon;
use Symfony\Component\Uid\Uuid;

readonly class UpdateDeliveryServiceRestrictAreaCommand implements Command
{
    public function __construct(
        private Uuid $deliveryServiceRestrictAreaId,
        private ?string $name = null,
        private ?Polygon $polygon = null,
        private ?bool $isActive = null
    ) {
    }

    public function getDeliveryServiceRestrictAreaId(): Uuid
    {
        return $this->deliveryServiceRestrictAreaId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getPolygon(): ?Polygon
    {
        return $this->polygon;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }
}
