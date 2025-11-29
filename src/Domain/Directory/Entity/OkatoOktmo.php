<?php

namespace App\Domain\Directory\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'okato_oktmo', options: [
    'comment' => 'Таблица соответствия ОКАТО и ОКТМО',
])]
#[ORM\Entity]
#[ORM\Index(columns: ['oktmo'], name: 'idx_okato_oktmo_oktmo')]
#[ORM\UniqueConstraint(name: 'uniq_okato_oktmo_okato', columns: ['okato'])]
class OkatoOktmo
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'bigint', nullable: false, options: [
        'unsigned' => true,
        'comment' => 'Первичный ключ',
    ])]
    private int $id;

    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: false, options: [
        'comment' => 'Код ОКАТО',
    ])]
    private string $okato;

    #[ORM\Column(type: 'string', length: 255, nullable: false, options: [
        'comment' => 'Код ОКТМО',
    ])]
    private string $oktmo;

    #[ORM\Column(type: 'string', length: 255, nullable: true, options: [
        'comment' => 'Наименование населенного пункта',
    ])]
    private ?string $location;

    public function __construct(string $okato, string $oktmo, ?string $location = null)
    {
        $this->okato = $okato;
        $this->oktmo = $oktmo;
        $this->location = $location;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOkato(): string
    {
        return $this->okato;
    }

    public function getOktmo(): string
    {
        return $this->oktmo;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function change(string $oktmo, string $location): void
    {
        $this->oktmo = $oktmo;
        $this->location = $location;
    }
}
