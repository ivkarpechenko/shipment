<?php

namespace App\Infrastructure\DBAL\Repository\Doctrine\DeliveryService;

use App\Domain\DeliveryService\Entity\DeliveryService;
use App\Domain\DeliveryService\Repository\DeliveryServiceRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Uid\Uuid;

readonly class DoctrineDeliveryServiceRepository implements DeliveryServiceRepositoryInterface
{
    public function __construct(public EntityManagerInterface $entityManager)
    {
    }

    public function create(DeliveryService $deliveryService): void
    {
        $this->entityManager->persist($deliveryService);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function update(DeliveryService $deliveryService): void
    {
        $this->entityManager->persist($deliveryService);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function ofId(Uuid $deliveryServiceId): ?DeliveryService
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(DeliveryService::class, 'deliveryService')
            ->select('deliveryService')
            ->where('deliveryService.id = :id')
            ->andWhere('deliveryService.isActive = true')
            ->setParameter('id', $deliveryServiceId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofIdDeactivated(Uuid $deliveryServiceId): ?DeliveryService
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(DeliveryService::class, 'deliveryService')
            ->select('deliveryService')
            ->where('deliveryService.id = :id')
            ->andWhere('deliveryService.isActive = false')
            ->setParameter('id', $deliveryServiceId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofCode(string $code): ?DeliveryService
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(DeliveryService::class, 'deliveryService')
            ->select('deliveryService')
            ->where('deliveryService.code = :code')
            ->andWhere('deliveryService.isActive = true')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofCodeDeactivated(string $code): ?DeliveryService
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(DeliveryService::class, 'deliveryService')
            ->select('deliveryService')
            ->where('deliveryService.code = :code')
            ->andWhere('deliveryService.isActive = false')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function all(?bool $isActive = null): array
    {
        $query = $this->entityManager
            ->createQueryBuilder()
            ->from(DeliveryService::class, 'deliveryService')
            ->select('deliveryService');

        if (!is_null($isActive)) {
            $query
                ->where('deliveryService.isActive = :isActive')
                ->setParameter('isActive', $isActive);
        }

        return $query->getQuery()->getResult();
    }

    public function paginate(int $page, int $offset): array
    {
        $query = $this->entityManager
            ->createQueryBuilder()
            ->from(DeliveryService::class, 'deliveryService')
            ->select('deliveryService')
            ->andWhere('deliveryService.isActive = true')
            ->getQuery();

        $paginator = new Paginator($query);
        $total = count($paginator);
        $pages = (int) ceil($total / $offset);

        $deliveryServices = $paginator
            ->getQuery()
            ->setFirstResult($page)
            ->setMaxResults($offset)
            ->getResult();

        return [
            'data' => $deliveryServices,
            'total' => $total,
            'pages' => $pages,
        ];
    }
}
