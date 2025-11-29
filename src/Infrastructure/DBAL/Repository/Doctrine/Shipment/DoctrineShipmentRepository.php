<?php

namespace App\Infrastructure\DBAL\Repository\Doctrine\Shipment;

use App\Domain\Shipment\Entity\Shipment;
use App\Domain\Shipment\Repository\ShipmentRepositoryInterface;
use App\Infrastructure\DBAL\Repository\Doctrine\DoctrineTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Uid\Uuid;

readonly class DoctrineShipmentRepository implements ShipmentRepositoryInterface
{
    use DoctrineTrait;

    public function __construct(public EntityManagerInterface $entityManager)
    {
    }

    public function create(Shipment $shipment): Uuid
    {
        $this->entityManager->persist($shipment);
        $this->entityManager->flush();

        $this->entityManager->clear();

        return $shipment->getId();
    }

    public function update(Shipment $shipment): void
    {
        $this->entityManager->persist($shipment);
        $this->entityManager->flush();

        $this->entityManager->clear();
    }

    public function all(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Shipment::class, 'shipment')
            ->select('shipment')
            ->getQuery()
            ->getResult();
    }

    public function paginate(int $page, int $offset): array
    {
        $query = $this->entityManager
            ->createQueryBuilder()
            ->from(Shipment::class, 'shipment')
            ->select('shipment')
            ->getQuery();

        $paginator = new Paginator($query);
        $total = count($paginator);
        $pages = (int) ceil($total / $offset);

        $regions = $paginator
            ->getQuery()
            ->setFirstResult($page)
            ->setMaxResults($offset)
            ->getResult();

        return [
            'data' => $regions,
            'total' => $total,
            'pages' => $pages,
        ];
    }

    public function ofId(Uuid $shipmentId): ?Shipment
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Shipment::class, 'shipment')
            ->select('shipment')
            ->where('shipment.id = :id')
            ->setParameter('id', $shipmentId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofShipments(array $shipments): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Shipment::class, 'shipment')
            ->select('shipment')
            ->where('shipment.id IN (:shipments)')
            ->setParameter('shipments', $this->convertUUIDsByPlatform($shipments))
            ->getQuery()
            ->getResult();
    }
}
