<?php

namespace App\Infrastructure\DBAL\Repository\Doctrine\Directory;

use App\Domain\Directory\Entity\OkatoOktmo;
use App\Domain\Directory\Repository\OkatoOktmoRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineOkatoOktmoRepository implements OkatoOktmoRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function create(OkatoOktmo $okato): void
    {
        $this->entityManager->persist($okato);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function update(OkatoOktmo $okato): void
    {
        $this->entityManager->persist($okato);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function ofOkato(string $okato): ?OkatoOktmo
    {
        return $this->entityManager
            ->getRepository(OkatoOktmo::class)
            ->findOneBy(['okato' => $okato]);
    }
}
