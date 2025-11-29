<?php

namespace App\Infrastructure\DBAL\Repository\Doctrine\Region;

use App\Domain\Country\Entity\Country;
use App\Domain\Region\Entity\Region;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Uid\Uuid;

class DoctrineRegionRepository implements RegionRepositoryInterface
{
    public function __construct(public EntityManagerInterface $entityManager)
    {
    }

    public function create(Region $region): void
    {
        $this->entityManager->persist($region);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function update(Region $region): void
    {
        $this->entityManager->persist($region);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function delete(Region $region): void
    {
        $this->entityManager->persist($region);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function restore(Region $region): void
    {
        $this->entityManager->persist($region);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function all(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Region::class, 'region')
            ->select('region')
            ->getQuery()
            ->getResult();
    }

    public function paginate(int $page, int $offset): array
    {
        $query = $this->entityManager
            ->createQueryBuilder()
            ->from(Region::class, 'region')
            ->select('region')
            ->andWhere('region.deletedAt IS NULL')
            ->andWhere('region.isActive = true')
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

    public function ofId(Uuid $regionId): ?Region
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Region::class, 'region')
            ->select('region')
            ->where('region.id = :id')
            ->andWhere('region.deletedAt IS NULL')
            ->andWhere('region.isActive = true')
            ->setParameter('id', $regionId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofCountry(Country $country): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Region::class, 'region')
            ->select('region')
            ->where('region.country = :country')
            ->andWhere('region.deletedAt IS NULL')
            ->andWhere('region.isActive = true')
            ->setParameter('country', $country->getId(), 'uuid')
            ->getQuery()
            ->getResult();
    }

    public function ofIdDeleted(Uuid $regionId): ?Region
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Region::class, 'region')
            ->select('region')
            ->where('region.id = :id')
            ->andWhere('region.deletedAt IS NOT NULL')
            ->setParameter('id', $regionId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofIdDeactivated(Uuid $regionId): ?Region
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Region::class, 'region')
            ->select('region')
            ->where('region.id = :id')
            ->andWhere('region.deletedAt IS NULL')
            ->andWhere('region.isActive = false')
            ->setParameter('id', $regionId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofCode(string $code): ?Region
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Region::class, 'region')
            ->select('region')
            ->where('region.code = :code')
            ->andWhere('region.deletedAt IS NULL')
            ->andWhere('region.isActive = true')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofCodeDeactivated(string $code): ?Region
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Region::class, 'region')
            ->select('region')
            ->where('region.code = :code')
            ->andWhere('region.isActive = false')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofCodeDeleted(string $code): ?Region
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Region::class, 'region')
            ->select('region')
            ->where('region.code = :code')
            ->andWhere('region.deletedAt IS NOT NULL')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofName(string $name): ?Region
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Region::class, 'region')
            ->select('region')
            ->where('region.name = :name')
            ->andWhere('region.deletedAt IS NULL')
            ->andWhere('region.isActive = true')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
