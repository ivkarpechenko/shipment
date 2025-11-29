<?php

namespace App\Infrastructure\DBAL\Repository\Doctrine\Shipment;

use App\Domain\Shipment\Entity\Product;
use App\Domain\Shipment\Repository\ProductRepositoryInterface;
use App\Infrastructure\DBAL\Repository\Doctrine\DoctrineTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class DoctrineProductRepository implements ProductRepositoryInterface
{
    use DoctrineTrait;

    public function __construct(public EntityManagerInterface $entityManager)
    {
    }

    public function create(Product $product): Uuid
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $this->entityManager->clear();

        return $product->getId();
    }

    public function ofId(Uuid $productId): ?Product
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Product::class, 'product')
            ->select('product')
            ->where('product.id = :id')
            ->setParameter('id', $productId, 'uuid')
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }

    public function ofStores(array $stores): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Product::class, 'product')
            ->select('product')
            ->innerJoin('product.store', 'store')
            ->where('store.id IN (:stores)')
            ->setParameter('stores', $this->convertUUIDsByPlatform($stores))
            ->getQuery()
            ->getResult();
    }

    public function ofStoreAndDeliveryPeriod(Uuid $storeId, int $deliveryPeriod): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Product::class, 'product')
            ->select('product')
            ->innerJoin('product.store', 'store')
            ->where('product.deliveryPeriod = :deliveryPeriod')
            ->andWhere('store.id = :storeId')
            ->setParameter('deliveryPeriod', $deliveryPeriod)
            ->setParameter('storeId', $storeId, 'uuid')
            ->getQuery()
            ->getResult();
    }
}
