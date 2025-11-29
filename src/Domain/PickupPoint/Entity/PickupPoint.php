<?php

namespace App\Domain\PickupPoint\Entity;

use App\Domain\Address\ValueObject\Point;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\PickupPoint\Repository\PickupPointRepositoryInterface;
use App\Domain\PickupPoint\Service\Dto\PickupPointDto;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'pickup_point')]
#[ORM\UniqueConstraint(
    name: 'pickup_point_unique_delivery_service_id_code',
    columns: ['delivery_service_id', 'code']
)]
#[ORM\Entity(repositoryClass: PickupPointRepositoryInterface::class)]
#[ORM\Index(columns: ['address'], name: 'idx_pickup_point_address')]
class PickupPoint
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\ManyToOne(targetEntity: DeliveryService::class)]
    #[ORM\JoinColumn(nullable: false)]
    private DeliveryService $deliveryService;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private string $name;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private string $code;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    private string $type;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2, nullable: true)]
    private ?float $weightMin = null;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2, nullable: true)]
    private ?float $weightMax = null;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $workTime;

    #[ORM\Column(type: 'string', length: 255, nullable: false)]
    private string $address;

    #[ORM\Column(type: 'json', nullable: false, options: [
        'jsonb' => true,
    ])]
    private array $phones = [];

    #[ORM\Column(type: 'point', nullable: false, options: [
        'comment' => 'Широта и Долгота',
    ])]
    private Point $point;

    #[ORM\Column(type: 'boolean', options: ['default' => 1])]
    private bool $isActive = true;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2, nullable: true)]
    private ?float $width = null;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2, nullable: true)]
    private ?float $height = null;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2, nullable: true)]
    private ?float $depth = null;

    #[ORM\Column(type: 'datetime', nullable: false)]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $updatedAt = null;

    public function __construct(
        DeliveryService $deliveryService,
        array $phones,
        Point $point,
        string $address,
        string $workTime,
        string $name,
        string $code,
        string $type,
        ?float $weightMin,
        ?float $weightMax,
        ?float $width,
        ?float $height,
        ?float $depth,
    ) {
        $this->phones = $phones;
        $this->point = $point;
        $this->address = $address;
        $this->workTime = $workTime;
        $this->name = $name;
        $this->deliveryService = $deliveryService;
        $this->code = $code;
        $this->type = $type;
        $this->weightMin = $weightMin;
        $this->weightMax = $weightMax;
        $this->width = $width;
        $this->height = $height;
        $this->depth = $depth;
        $this->createdAt = new \DateTime();
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getWorkTime(): string
    {
        return $this->workTime;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getPhones(): array
    {
        return $this->phones;
    }

    public function getPoint(): Point
    {
        return $this->point;
    }

    public function getDeliveryService(): DeliveryService
    {
        return $this->deliveryService;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getWeightMin(): ?float
    {
        return $this->weightMin;
    }

    public function getWeightMinGram(): ?float
    {
        if (!is_null($this->weightMin)) {
            return $this->weightMin * 1000;
        }

        return $this->weightMin;
    }

    public function getWeightMax(): ?float
    {
        return $this->weightMax;
    }

    public function getWeightMaxGram(): ?float
    {
        if (!is_null($this->weightMax)) {
            return $this->weightMax * 1000;
        }

        return $this->weightMin;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getWidth(): ?float
    {
        return $this->width;
    }

    public function getWidthMM(): ?float
    {
        if (!is_null($this->width)) {
            return $this->width * 10;
        }

        return $this->width;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function getHeightMM(): ?float
    {
        if (!is_null($this->height)) {
            return $this->height * 10;
        }

        return $this->height;
    }

    public function getDepth(): ?float
    {
        return $this->depth;
    }

    public function getDepthMM(): ?float
    {
        if (!is_null($this->depth)) {
            return $this->depth * 10;
        }

        return $this->depth;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function getMaxDimension(): ?float
    {
        return max($this->width, $this->height, $this->depth);
    }

    public function changeIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
        $this->updatedAt = new \DateTime('now');
    }

    public function change(PickupPointDto $dto): void
    {
        $this->name = $dto->name;
        $this->phones = $dto->phones;
        $this->workTime = $dto->workTime;
        $this->address = $dto->address;
        $this->type = $dto->type;
        $this->weightMin = $dto->weightMin;
        $this->weightMax = $dto->weightMax;
        $this->width = $dto->width;
        $this->height = $dto->height;
        $this->depth = $dto->depth;
        $this->updatedAt = new \DateTime('now');
    }
}
