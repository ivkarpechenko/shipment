<?php

namespace App\Infrastructure\DBAL\Repository\Doctrine\Shipment;

use App\Domain\Shipment\Entity\Calculate;
use App\Domain\Shipment\Entity\Shipment;
use App\Domain\Shipment\Repository\CalculateRepositoryInterface;
use App\Domain\TariffPlan\Entity\TariffPlan;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Uid\Uuid;

readonly class DoctrineCalculateRepository implements CalculateRepositoryInterface
{
    public function __construct(public EntityManagerInterface $entityManager)
    {
    }

    public function create(Calculate $calculate): Uuid
    {
        $this->entityManager->persist($calculate);
        $this->entityManager->flush();

        $this->entityManager->clear();

        return $calculate->getId();
    }

    public function update(Calculate $calculate): Uuid
    {
        $this->entityManager->persist($calculate);
        $this->entityManager->flush();

        $this->entityManager->clear();

        return $calculate->getId();
    }

    public function paginate(int $page, int $offset): array
    {
        $query = $this->entityManager
            ->createQueryBuilder()
            ->from(Calculate::class, 'calculate')
            ->select('calculate')
            ->where('calculate.expiredAt > :expiredAt')
            ->setParameter('expiredAt', new \DateTime('now'))
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

    public function ofIdNotExpired(Uuid $calculateId): ?Calculate
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Calculate::class, 'calculate')
            ->select('calculate')
            ->where('calculate.id = :id')
            ->andWhere('calculate.expiredAt > :expiredAt')
            ->setParameter('id', $calculateId, 'uuid')
            ->setParameter('expiredAt', new \DateTime('now'))
            ->orderBy('calculate.createdAt', 'DESC')
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    public function ofShipmentIdNotExpired(Uuid $shipmentId): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Calculate::class, 'calculate')
            ->select('calculate')
            ->innerJoin(Shipment::class, 'shipment', Join::WITH, 'calculate.shipment = shipment')
            ->where('calculate.expiredAt > :expiredAt')
            ->andWhere('shipment.id = :id')
            ->setParameter('expiredAt', new \DateTime('now'))
            ->setParameter('id', $shipmentId, 'uuid')
            ->orderBy('calculate.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function ofShipmentAndTariffPlanIdNotExpired(Uuid $shipmentId, Uuid $tariffPlanId): ?Calculate
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Calculate::class, 'calculate')
            ->select('calculate')
            ->innerJoin(Shipment::class, 'shipment', Join::WITH, 'calculate.shipment = shipment')
            ->innerJoin(TariffPlan::class, 'tariffPlan', Join::WITH, 'calculate.tariffPlan = tariffPlan')
            ->where('calculate.expiredAt > :expiredAt')
            ->andWhere('shipment.id = :shipmentId')
            ->andWhere('tariffPlan.id = :tariffPlanId')
            ->setParameter('expiredAt', new \DateTime('now'))
            ->setParameter('shipmentId', $shipmentId, 'uuid')
            ->setParameter('tariffPlanId', $tariffPlanId, 'uuid')
            ->orderBy('calculate.createdAt', 'DESC')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
