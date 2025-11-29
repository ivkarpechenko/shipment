<?php

namespace App\Infrastructure\DBAL\Repository\Doctrine\Shipment;

use App\Domain\Shipment\Entity\Store;
use App\Domain\Shipment\Repository\StoreRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class DoctrineStoreRepository implements StoreRepositoryInterface
{
    public function __construct(public EntityManagerInterface $entityManager)
    {
    }

    public function create(Store $store): Uuid
    {
        $this->entityManager->persist($store);
        $this->entityManager->flush();

        $this->entityManager->clear();

        return $store->getId();
    }

    public function ofId(Uuid $storeId): ?Store
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Store::class, 'store')
            ->select('store')
            ->where('store.id = :id')
            ->setParameter('id', $storeId, 'uuid')
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    public function ofExternalId(int $externalId): ?Store
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Store::class, 'store')
            ->select('store')
            ->where('store.externalId = :externalId')
            ->setParameter('externalId', $externalId)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    public function ofIdAndExternalId(Uuid $storeId, int $externalId): ?Store
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Store::class, 'store')
            ->select('store')
            ->where('store.id = :id')
            ->andWhere('store.externalId = :externalId')
            ->setParameter('id', $storeId, 'uuid')
            ->setParameter('externalId', $externalId)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }
}
