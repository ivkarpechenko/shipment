<?php

declare(strict_types=1);

namespace App\Infrastructure\DBAL\Repository\Doctrine\DeliveryMethod;

use App\Domain\DeliveryMethod\Entity\DeliveryMethod;
use App\Domain\DeliveryMethod\Repository\DeliveryMethodRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

readonly class DoctrineDeliveryMethodRepository implements DeliveryMethodRepositoryInterface
{
    public function __construct(
        public EntityManagerInterface $entityManager
    ) {
    }

    public function create(DeliveryMethod $deliveryMethod): void
    {
        $this->entityManager->persist($deliveryMethod);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function update(DeliveryMethod $deliveryMethod): void
    {
        $this->entityManager->persist($deliveryMethod);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function ofCode(string $code): ?DeliveryMethod
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(DeliveryMethod::class, 'deliveryMethod')
            ->select('deliveryMethod')
            ->andWhere('deliveryMethod.code = :code')
            ->andWhere('deliveryMethod.isActive = true')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofCodeDeactivated(string $code): ?DeliveryMethod
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(DeliveryMethod::class, 'deliveryMethod')
            ->select('deliveryMethod')
            ->andWhere('deliveryMethod.code = :code')
            ->andWhere('deliveryMethod.isActive = false')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofId(Uuid $id): ?DeliveryMethod
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(DeliveryMethod::class, 'deliveryMethod')
            ->select('deliveryMethod')
            ->andWhere('deliveryMethod.id = :id')
            ->setParameter('id', $id, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function all(?bool $isActive = null): array
    {
        $query = $this->entityManager
            ->createQueryBuilder()
            ->from(DeliveryMethod::class, 'deliveryMethod')
            ->select('deliveryMethod');

        if (!is_null($isActive)) {
            $query
                ->where('deliveryMethod.isActive = :isActive')
                ->setParameter('isActive', $isActive);
        }

        return $query->getQuery()->getResult();
    }
}
