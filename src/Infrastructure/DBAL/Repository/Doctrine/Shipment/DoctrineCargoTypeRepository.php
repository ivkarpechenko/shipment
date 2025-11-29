<?php

declare(strict_types=1);

namespace App\Infrastructure\DBAL\Repository\Doctrine\Shipment;

use App\Domain\Shipment\Entity\CargoType;
use App\Domain\Shipment\Repository\CargoTypeRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

readonly class DoctrineCargoTypeRepository implements CargoTypeRepositoryInterface
{
    public function __construct(
        public EntityManagerInterface $entityManager
    ) {
    }

    public function create(CargoType $cargoType): void
    {
        $this->entityManager->persist($cargoType);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function update(CargoType $cargoType): void
    {
        $this->entityManager->persist($cargoType);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function ofId(Uuid $id): ?CargoType
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(CargoType::class, 'cargoType')
            ->select('cargoType')
            ->where('cargoType.id = :id')
            ->setParameter('id', $id, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofCode(string $code): ?CargoType
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(CargoType::class, 'cargoType')
            ->select('cargoType')
            ->andWhere('cargoType.code = :code')
            ->andWhere('cargoType.isActive = true')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofCodeDeactivated(string $code): ?CargoType
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(CargoType::class, 'cargoType')
            ->select('cargoType')
            ->andWhere('cargoType.code = :code')
            ->andWhere('cargoType.isActive = false')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return CargoType[]
     */
    public function all(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(CargoType::class, 'cargoType')
            ->select('cargoType')
            ->getQuery()
            ->getResult();
    }
}
