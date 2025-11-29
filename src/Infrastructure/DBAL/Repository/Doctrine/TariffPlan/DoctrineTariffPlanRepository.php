<?php

namespace App\Infrastructure\DBAL\Repository\Doctrine\TariffPlan;

use App\Domain\DeliveryMethod\Entity\DeliveryMethod;
use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\TariffPlan\Entity\TariffPlan;
use App\Domain\TariffPlan\Repository\TariffPlanRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Uid\Uuid;

readonly class DoctrineTariffPlanRepository implements TariffPlanRepositoryInterface
{
    public function __construct(public EntityManagerInterface $entityManager)
    {
    }

    public function create(TariffPlan $tariffPlan): void
    {
        $this->entityManager->persist($tariffPlan);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function update(TariffPlan $tariffPlan): void
    {
        $this->entityManager->persist($tariffPlan);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function ofId(Uuid $tariffPlanId): ?TariffPlan
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(TariffPlan::class, 'tariffPlan')
            ->select('tariffPlan')
            ->where('tariffPlan.id = :id')
            ->andWhere('tariffPlan.isActive = true')
            ->setParameter('id', $tariffPlanId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofIdDeactivated(Uuid $tariffPlanId): ?TariffPlan
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(TariffPlan::class, 'tariffPlan')
            ->select('tariffPlan')
            ->where('tariffPlan.id = :id')
            ->andWhere('tariffPlan.isActive = false')
            ->setParameter('id', $tariffPlanId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofCode(string $deliveryServiceCode, string $deliveryMethodCode, string $code): ?TariffPlan
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(TariffPlan::class, 'tariffPlan')
            ->select('tariffPlan')
            ->innerJoin(DeliveryService::class, 'deliveryService', Join::WITH, 'tariffPlan.deliveryService = deliveryService')
            ->innerJoin(DeliveryMethod::class, 'deliveryMethod', Join::WITH, 'tariffPlan.deliveryMethod = deliveryMethod')
            ->where('tariffPlan.code = :code')
            ->andWhere('tariffPlan.isActive = true')
            ->andWhere('deliveryService.isActive = true')
            ->andWhere('deliveryService.code = :deliveryServiceCode')
            ->andWhere('deliveryMethod.isActive = true')
            ->andWhere('deliveryMethod.code = :deliveryMethodCode')
            ->setParameter('code', $code)
            ->setParameter('deliveryServiceCode', $deliveryServiceCode)
            ->setParameter('deliveryMethodCode', $deliveryMethodCode)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofCodeDeactivated(string $deliveryServiceCode, string $deliveryMethodCode, string $code): ?TariffPlan
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(TariffPlan::class, 'tariffPlan')
            ->select('tariffPlan')
            ->innerJoin(DeliveryService::class, 'deliveryService', Join::WITH, 'tariffPlan.deliveryService = deliveryService')
            ->innerJoin(DeliveryMethod::class, 'deliveryMethod', Join::WITH, 'tariffPlan.deliveryMethod = deliveryMethod')
            ->where('tariffPlan.code = :code')
            ->andWhere('tariffPlan.isActive = false')
            ->andWhere('deliveryService.isActive = true')
            ->andWhere('deliveryService.code = :deliveryServiceCode')
            ->andWhere('deliveryMethod.isActive = true')
            ->andWhere('deliveryMethod.code = :deliveryMethodCode')
            ->setParameter('code', $code)
            ->setParameter('deliveryServiceCode', $deliveryServiceCode)
            ->setParameter('deliveryMethodCode', $deliveryMethodCode)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function all(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(TariffPlan::class, 'tariffPlan')
            ->select('tariffPlan')
            ->getQuery()
            ->getResult();
    }

    public function paginate(int $page, int $offset): array
    {
        $query = $this->entityManager
            ->createQueryBuilder()
            ->from(TariffPlan::class, 'tariffPlan')
            ->select('tariffPlan')
            ->andWhere('tariffPlan.isActive = true')
            ->getQuery();

        $paginator = new Paginator($query);
        $total = count($paginator);
        $pages = (int) ceil($total / $offset);

        $countries = $paginator
            ->getQuery()
            ->setFirstResult($page)
            ->setMaxResults($offset)
            ->getResult();

        return [
            'data' => $countries,
            'total' => $total,
            'pages' => $pages,
        ];
    }

    public function active(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(TariffPlan::class, 'tariffPlan')
            ->select('tariffPlan')
            ->where('tariffPlan.isActive = true')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return TariffPlan[]
     */
    public function ofServiceAndMethod(string $deliveryServiceCode, string $deliveryMethodCode, ?bool $isActive = null): array
    {
        $query = $this->entityManager
            ->createQueryBuilder()
            ->from(TariffPlan::class, 'tariffPlan')
            ->select('tariffPlan')
            ->innerJoin(DeliveryService::class, 'deliveryService', Join::WITH, 'tariffPlan.deliveryService = deliveryService')
            ->innerJoin(DeliveryMethod::class, 'deliveryMethod', Join::WITH, 'tariffPlan.deliveryMethod = deliveryMethod')
            ->andWhere('deliveryService.code = :deliveryServiceCode')
            ->andWhere('deliveryMethod.code = :deliveryMethodCode')
            ->setParameter('deliveryServiceCode', $deliveryServiceCode)
            ->setParameter('deliveryMethodCode', $deliveryMethodCode);

        if (!is_null($isActive)) {
            if ($isActive) {
                $query
                    ->andWhere('tariffPlan.isActive = true')
                    ->andWhere('deliveryService.isActive = true')
                    ->andWhere('deliveryMethod.isActive = true');
            } else {
                $query
                    ->orWhere('tariffPlan.isActive = false')
                    ->orWhere('deliveryService.isActive = false')
                    ->orWhere('deliveryMethod.isActive = false');
            }
        }

        return $query->getQuery()->getResult();
    }
}
