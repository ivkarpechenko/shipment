<?php

declare(strict_types=1);

namespace App\Infrastructure\DBAL\Repository\Doctrine\DeliveryService;

use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Entity\DeliveryServiceRestrictPackage;
use App\Domain\DeliveryService\Repository\DeliveryServiceRestrictPackageRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\Uid\Uuid;

readonly class DoctrineDeliveryServiceRestrictPackageRepository implements DeliveryServiceRestrictPackageRepositoryInterface
{
    public function __construct(
        public EntityManagerInterface $entityManager
    ) {
    }

    public function create(DeliveryServiceRestrictPackage $deliveryServiceRestrictPackage): void
    {
        $this->entityManager->persist($deliveryServiceRestrictPackage);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function update(DeliveryServiceRestrictPackage $deliveryServiceRestrictPackage): void
    {
        $this->entityManager->persist($deliveryServiceRestrictPackage);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function all(?bool $isActive = null): array
    {
        $query = $this->entityManager
            ->createQueryBuilder()
            ->from(DeliveryServiceRestrictPackage::class, 'dsrp')
            ->select('dsrp');

        if (!is_null($isActive)) {
            $query
                ->andWhere('dsrp.isActive = :isActive')
                ->setParameter('isActive', $isActive);
        }

        return $query->getQuery()->getResult();
    }

    public function ofId(Uuid $id): ?DeliveryServiceRestrictPackage
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(DeliveryServiceRestrictPackage::class, 'dsrp')
            ->select('dsrp')
            ->where('dsrp.id = :id')
            ->andWhere('dsrp.isActive = true')
            ->setParameter('id', $id, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofIdDeactivated(Uuid $id): ?DeliveryServiceRestrictPackage
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(DeliveryServiceRestrictPackage::class, 'dsrp')
            ->select('dsrp')
            ->where('dsrp.id = :id')
            ->andWhere('dsrp.isActive = false')
            ->setParameter('id', $id, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofDeliveryServiceId(Uuid $deliveryServiceId): ?DeliveryServiceRestrictPackage
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(DeliveryServiceRestrictPackage::class, 'dsrp')
            ->select('dsrp')
            ->innerJoin(DeliveryService::class, 'deliveryService', Join::WITH, 'dsrp.deliveryService = deliveryService')
            ->where('dsrp.isActive = true')
            ->andWhere('deliveryService.id = :id')
            ->setParameter('id', $deliveryServiceId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofDeliveryServiceIdDeactivated(Uuid $deliveryServiceId): ?DeliveryServiceRestrictPackage
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(DeliveryServiceRestrictPackage::class, 'dsrp')
            ->select('dsrp')
            ->innerJoin(DeliveryService::class, 'deliveryService', Join::WITH, 'dsrp.deliveryService = deliveryService')
            ->where('dsrp.isActive = false')
            ->andWhere('deliveryService.id = :id')
            ->setParameter('id', $deliveryServiceId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
