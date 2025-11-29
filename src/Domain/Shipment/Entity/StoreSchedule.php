<?php

namespace App\Domain\Shipment\Entity;

use App\Domain\Shipment\ValueObject\Day;
use App\Domain\Shipment\ValueObject\EndTime;
use App\Domain\Shipment\ValueObject\StartTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Table(name: 'store_schedule', options: [
    'comment' => 'График работы склада',
])]
#[ORM\Entity]
class StoreSchedule
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true, options: [
        'comment' => 'Уникальный идентификатор',
    ])]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private Uuid $id;

    #[ORM\Column(type: 'day', options: [
        'comment' => 'День недели',
    ])]
    private Day $day;

    #[ORM\Column(type: 'startTime', options: [
        'comment' => 'Начало рабочего дня.',
    ])]
    private StartTime $startTime;

    #[ORM\Column(type: 'endTime', options: [
        'comment' => 'Конец рабочего дня.',
    ])]
    private EndTime $endTime;

    #[ORM\ManyToOne(targetEntity: Store::class, inversedBy: 'schedules')]
    #[ORM\JoinColumn(name: 'store', referencedColumnName: 'id', nullable: true, onDelete: 'CASCADE')]
    private ?Store $store = null;

    public function __construct(
        Day $day,
        StartTime $startTime,
        EndTime $endTime
    ) {
        $this->day = $day;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getDay(): Day
    {
        return $this->day;
    }

    public function getStartTime(): StartTime
    {
        return $this->startTime;
    }

    public function getEndTime(): EndTime
    {
        return $this->endTime;
    }

    public function getStore(): ?Store
    {
        return $this->store;
    }

    public function setStore(?Store $store): void
    {
        $this->store = $store;
    }
}
