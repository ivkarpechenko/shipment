<?php

namespace App\Domain\Shipment\Entity;

use App\Domain\Shipment\Dto\CalculateDto;
use App\Domain\Shipment\Repository\CalculateRepositoryInterface;
use App\Domain\TariffPlan\Entity\TariffPlan;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: CalculateRepositoryInterface::class)]
#[ORM\Table('calculate')]
#[ORM\Index(columns: ['created_at'], name: 'idx_calculate_created_at')]
#[ORM\Index(columns: ['expired_at'], name: 'idx_calculate_expired_at')]
class Calculate
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: Shipment::class)]
    #[ORM\JoinColumn(name: 'shipment', referencedColumnName: 'id', nullable: false)]
    private Shipment $shipment;

    #[ORM\ManyToOne(targetEntity: TariffPlan::class)]
    #[ORM\JoinColumn(name: 'tariff_plan', referencedColumnName: 'id', nullable: false)]
    private TariffPlan $tariffPlan;

    #[ORM\Column(type: 'integer', length: 10, nullable: false)]
    private int $minPeriod;

    #[ORM\Column(type: 'integer', length: 10, nullable: false)]
    private int $maxPeriod;

    #[ORM\Column(type: 'float', nullable: false)]
    private float $deliveryCost;

    #[ORM\Column(type: 'float', nullable: false)]
    private float $deliveryTotalCost;

    #[ORM\Column(type: 'float', nullable: false, options: ['default' => 0.0])]
    private float $deliveryTotalCostTax;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $expiredAt;

    public function __construct(
        Shipment $shipment,
        TariffPlan $tariffPlan,
        int $minPeriod,
        int $maxPeriod,
        float $deliveryCost,
        float $deliveryTotalCost,
        float $deliveryTotalCostTax
    ) {
        $this->shipment = $shipment;
        $this->tariffPlan = $tariffPlan;
        $this->minPeriod = $minPeriod;
        $this->maxPeriod = $maxPeriod;
        $this->deliveryCost = $deliveryCost;
        $this->deliveryTotalCost = $deliveryTotalCost;
        $this->deliveryTotalCostTax = $deliveryTotalCostTax;

        $this->createdAt = new \DateTime('now');
        $this->expiredAt = new \DateTime('+1 hour');
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getShipment(): Shipment
    {
        return $this->shipment;
    }

    public function getTariffPlan(): TariffPlan
    {
        return $this->tariffPlan;
    }

    public function getMinPeriod(): int
    {
        return $this->minPeriod;
    }

    public function getMaxPeriod(): int
    {
        return $this->maxPeriod;
    }

    public function getDeliveryCost(): float
    {
        return $this->deliveryCost;
    }

    public function getDeliveryTotalCost(): float
    {
        return $this->deliveryTotalCost;
    }

    public function getDeliveryTotalCostTax(): float
    {
        return $this->deliveryTotalCostTax;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getExpiredAt(): \DateTime
    {
        return $this->expiredAt;
    }

    public function changeExpiredAt(\DateTime $expiredAt): void
    {
        $this->expiredAt = $expiredAt;
    }

    public function change(CalculateDto $calculateDto): void
    {
        $this->minPeriod = $calculateDto->minPeriod;
        $this->maxPeriod = $calculateDto->maxPeriod;
        $this->deliveryCost = $calculateDto->deliveryCost;
        $this->deliveryTotalCost = $calculateDto->deliveryTotalCost;
        $this->deliveryTotalCostTax = $calculateDto->deliveryTotalCostTax;
    }
}
