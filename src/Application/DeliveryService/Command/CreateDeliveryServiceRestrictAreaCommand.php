<?php

namespace App\Application\DeliveryService\Command;

use App\Application\Command;
use App\Domain\DeliveryService\ValueObject\Polygon;
use Symfony\Component\Uid\Uuid;

readonly class CreateDeliveryServiceRestrictAreaCommand implements Command
{
    public function __construct(private Uuid $deliveryServiceId, private string $name, private Polygon $polygon)
    {
    }

    public function getDeliveryServiceId(): Uuid
    {
        return $this->deliveryServiceId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPolygon(): Polygon
    {
        return $this->polygon;
    }
}
