<?php

namespace App\Infrastructure\DBAL\Repository\Doctrine\PickupPoint;

use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\PickupPoint\Entity\PickupPoint;
use App\Domain\PickupPoint\Repository\PickupPointRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Uid\Uuid;

class DoctrinePickupPointRepository implements PickupPointRepositoryInterface
{
    public function __construct(public EntityManagerInterface $entityManager)
    {
    }

    public function create(PickupPoint $pickupPoint): void
    {
        $this->entityManager->persist($pickupPoint);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function update(PickupPoint $pickupPoint): void
    {
        $this->entityManager->persist($pickupPoint);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function all(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(PickupPoint::class, 'pickup_point')
            ->innerJoin(DeliveryService::class, 'deliveryService', Join::WITH, 'pickup_point.deliveryService = deliveryService')
            ->select('pickup_point')
            ->where('deliveryService.isActive = true')
            ->andWhere('pickup_point.isActive = true')
            ->getQuery()
            ->getResult();
    }

    public function paginate(int $page, int $offset): array
    {
        $query = $this->entityManager
            ->createQueryBuilder()
            ->from(PickupPoint::class, 'pickup_point')
            ->select('pickup_point')
            ->andWhere('pickup_point.isActive = true')
            ->getQuery();

        $paginator = new Paginator($query);
        $total = count($paginator);
        $pages = (int) ceil($total / $offset);

        $cities = $paginator
            ->getQuery()
            ->setFirstResult($page)
            ->setMaxResults($offset)
            ->getResult();

        return [
            'data' => $cities,
            'total' => $total,
            'pages' => $pages,
        ];
    }

    public function ofId(Uuid $pickupPointId): ?PickupPoint
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(PickupPoint::class, 'pickup_point')
            ->select('pickup_point')
            ->where('pickup_point.id = :id')
            ->setParameter('id', $pickupPointId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofDeliveryServiceAndCode(DeliveryService $deliveryService, string $code): ?PickupPoint
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(PickupPoint::class, 'pickup_point')
            ->select('pickup_point')
            ->where('pickup_point.deliveryService = :deliveryService')
            ->andWhere('pickup_point.code = :code')
            ->andWhere('pickup_point.isActive = true')
            ->setParameter('deliveryService', $deliveryService->getId(), 'uuid')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofIdDeactivated(Uuid $pickupPointId): ?PickupPoint
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(PickupPoint::class, 'pickup_point')
            ->select('pickup_point')
            ->where('pickup_point.id = :id')
            ->andWhere('pickup_point.isActive = false')
            ->setParameter('id', $pickupPointId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
