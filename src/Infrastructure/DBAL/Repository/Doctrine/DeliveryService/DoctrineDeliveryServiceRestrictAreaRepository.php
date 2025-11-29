<?php

namespace App\Infrastructure\DBAL\Repository\Doctrine\DeliveryService;

use App\Domain\Address\ValueObject\Point;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Entity\DeliveryServiceRestrictArea;
use App\Domain\DeliveryService\Repository\DeliveryServiceRestrictAreaRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Uid\Uuid;

readonly class DoctrineDeliveryServiceRestrictAreaRepository implements DeliveryServiceRestrictAreaRepositoryInterface
{
    public function __construct(public EntityManagerInterface $entityManager)
    {
    }

    public function create(DeliveryServiceRestrictArea $deliveryServiceRestrictArea): DeliveryServiceRestrictArea
    {
        $this->entityManager->persist($deliveryServiceRestrictArea);
        $this->entityManager->flush();
        $this->entityManager->clear();

        return $deliveryServiceRestrictArea;
    }

    public function update(DeliveryServiceRestrictArea $deliveryServiceRestrictArea): void
    {
        $this->entityManager->persist($deliveryServiceRestrictArea);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function ofId(Uuid $deliveryServiceRestrictAreaId): ?DeliveryServiceRestrictArea
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(DeliveryServiceRestrictArea::class, 'dsra')
            ->select('dsra')
            ->where('dsra.id = :id')
            ->andWhere('dsra.isActive = true')
            ->setParameter('id', $deliveryServiceRestrictAreaId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofIdDeactivated(Uuid $deliveryServiceRestrictAreaId): ?DeliveryServiceRestrictArea
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(DeliveryServiceRestrictArea::class, 'dsra')
            ->select('dsra')
            ->where('dsra.id = :id')
            ->andWhere('dsra.isActive = false')
            ->setParameter('id', $deliveryServiceRestrictAreaId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofDeliveryServiceId(Uuid $deliveryServiceId): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(DeliveryServiceRestrictArea::class, 'dsra')
            ->select('dsra')
            ->innerJoin(DeliveryService::class, 'deliveryService', Join::WITH, 'dsra.deliveryService = deliveryService')
            ->where('dsra.isActive = true')
            ->andWhere('deliveryService.id = :id')
            ->setParameter('id', $deliveryServiceId, 'uuid')
            ->getQuery()
            ->getResult();
    }

    public function ofDeliveryServiceIdDeactivated(Uuid $deliveryServiceId): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(DeliveryServiceRestrictArea::class, 'dsra')
            ->select('dsra')
            ->innerJoin(DeliveryService::class, 'deliveryService', Join::WITH, 'dsra.deliveryService = deliveryService')
            ->where('dsra.isActive = false')
            ->andWhere('deliveryService.id = :id')
            ->setParameter('id', $deliveryServiceId, 'uuid')
            ->getQuery()
            ->getResult();
    }

    public function all(?bool $isActive = null): array
    {
        $query = $this->entityManager
            ->createQueryBuilder()
            ->from(DeliveryServiceRestrictArea::class, 'dsra')
            ->select('dsra');

        if (!is_null($isActive)) {
            $query
                ->where('dsra.isActive = :isActive')
                ->setParameter('isActive', $isActive);
        }

        return $query->getQuery()->getResult();
    }

    public function paginate(int $page, int $offset): array
    {
        $query = $this->entityManager
            ->createQueryBuilder()
            ->from(DeliveryServiceRestrictArea::class, 'dsra')
            ->select('dsra')
            ->andWhere('dsra.isActive = true')
            ->getQuery();

        $paginator = new Paginator($query);
        $total = count($paginator);
        $pages = (int) ceil($total / $offset);

        $restrictedAreas = $paginator
            ->getQuery()
            ->setFirstResult($page)
            ->setMaxResults($offset)
            ->getResult();

        return [
            'data' => $restrictedAreas,
            'total' => $total,
            'pages' => $pages,
        ];
    }

    public function ofDeliveryServiceIdAndPoint(Uuid $deliveryServiceId, Point $point): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(DeliveryServiceRestrictArea::class, 'dsra')
            ->select('dsra')
            ->innerJoin(DeliveryService::class, 'deliveryService', Join::WITH, 'dsra.deliveryService = deliveryService')
            ->where('dsra.isActive = true')
            ->andWhere('deliveryService.id = :id')
            ->andWhere('ST_Contains(dsra.polygon, ST_GeomFromText(:point)) = true')
            ->setParameter('id', $deliveryServiceId, 'uuid')
            ->setParameter('point', $point->toWKT())
            ->getQuery()
            ->getResult();
    }
}
