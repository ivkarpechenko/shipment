<?php

declare(strict_types=1);

namespace App\Infrastructure\DBAL\Repository\Doctrine\Shipment;

use App\Domain\Shipment\Entity\CargoRestriction;
use App\Domain\Shipment\Entity\CargoType;
use App\Domain\Shipment\Entity\Shipment;
use App\Domain\Shipment\Repository\CargoRestrictionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\Uid\Uuid;

readonly class DoctrineCargoRestrictionRepository implements CargoRestrictionRepositoryInterface
{
    public function __construct(
        public EntityManagerInterface $entityManager
    ) {
    }

    public function create(CargoRestriction $restriction): void
    {
        $this->entityManager->persist($restriction);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function ofId(Uuid $id): ?CargoRestriction
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(CargoRestriction::class, 'cargoRestriction')
            ->select('cargoRestriction')
            ->where('cargoRestriction.id = :id')
            ->setParameter('id', $id, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofShipmentIdAndCargoTypeCode(Uuid $shipmentId, string $cargoTypeCode): ?CargoRestriction
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(CargoRestriction::class, 'cargoRestriction')
            ->select('cargoRestriction')
            ->innerJoin(Shipment::class, 'shipment', Join::WITH, 'cargoRestriction.shipment = shipment')
            ->innerJoin(CargoType::class, 'cargoType', Join::WITH, 'cargoRestriction.cargoType = cargoType')
            ->where('shipment.id = :shipmentId')
            ->andWhere('cargoType.code = :cargoTypeCode')
            ->setParameter('shipmentId', $shipmentId, 'uuid')
            ->setParameter('cargoTypeCode', $cargoTypeCode)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return CargoRestriction[]
     */
    public function ofShipmentId(Uuid $shipmentId): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(CargoRestriction::class, 'cargoRestriction')
            ->select('cargoRestriction')
            ->innerJoin(Shipment::class, 'shipment', Join::WITH, 'cargoRestriction.shipment = shipment')
            ->where('shipment.id = :shipmentId')
            ->setParameter('shipmentId', $shipmentId, 'uuid')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return CargoRestriction[]
     */
    public function all(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(CargoRestriction::class, 'cargoRestriction')
            ->select('cargoRestriction')
            ->getQuery()
            ->getResult();
    }
}
