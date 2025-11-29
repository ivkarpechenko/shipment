<?php

namespace App\Infrastructure\DBAL\Repository\Doctrine\City;

use App\Domain\City\Entity\City;
use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\Region\Entity\Region;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Uid\Uuid;

class DoctrineCityRepository implements CityRepositoryInterface
{
    public function __construct(public EntityManagerInterface $entityManager)
    {
    }

    public function create(City $city): void
    {
        $this->entityManager->persist($city);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function update(City $city): void
    {
        $this->entityManager->persist($city);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function delete(City $city): void
    {
        $this->entityManager->persist($city);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function restore(City $city): void
    {
        $this->entityManager->persist($city);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function all(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(City::class, 'city')
            ->select('city')
            ->getQuery()
            ->getResult();
    }

    public function paginate(int $page, int $offset): array
    {
        $query = $this->entityManager
            ->createQueryBuilder()
            ->from(City::class, 'city')
            ->select('city')
            ->andWhere('city.deletedAt IS NULL')
            ->andWhere('city.isActive = true')
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

    public function ofId(Uuid $cityId): ?City
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(City::class, 'city')
            ->select('city')
            ->where('city.id = :id')
            ->andWhere('city.deletedAt IS NULL')
            ->andWhere('city.isActive = true')
            ->setParameter('id', $cityId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofRegion(Region $region): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(City::class, 'city')
            ->select('city')
            ->where('city.region = :region')
            ->andWhere('city.deletedAt IS NULL')
            ->andWhere('city.isActive = true')
            ->setParameter('region', $region->getId(), 'uuid')
            ->getQuery()
            ->getResult();
    }

    public function ofIdDeleted(Uuid $cityId): ?City
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(City::class, 'city')
            ->select('city')
            ->where('city.id = :id')
            ->andWhere('city.deletedAt IS NOT NULL')
            ->setParameter('id', $cityId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofIdDeactivated(Uuid $cityId): ?City
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(City::class, 'city')
            ->select('city')
            ->where('city.id = :id')
            ->andWhere('city.deletedAt IS NULL')
            ->andWhere('city.isActive = false')
            ->setParameter('id', $cityId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofTypeAndName(string $type, string $name): ?City
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(City::class, 'city')
            ->select('city')
            ->where('city.type = :type')
            ->andWhere('city.name = :name')
            ->andWhere('city.deletedAt IS NULL')
            ->andWhere('city.isActive = true')
            ->setParameter('type', $type)
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofTypeAndNameDeleted(string $type, string $name): ?City
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(City::class, 'city')
            ->select('city')
            ->where('city.type = :type')
            ->andWhere('city.name = :name')
            ->andWhere('city.deletedAt IS NOT NULL')
            ->setParameter('type', $type)
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofTypeAndNameDeactivated(string $type, string $name): ?City
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(City::class, 'city')
            ->select('city')
            ->where('city.type = :type')
            ->andWhere('city.name = :name')
            ->andWhere('city.isActive = false')
            ->setParameter('type', $type)
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofType(string $type): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(City::class, 'city')
            ->select('city')
            ->where('city.type = :type')
            ->andWhere('city.deletedAt IS NULL')
            ->andWhere('city.isActive = true')
            ->setParameter('type', $type)
            ->getQuery()
            ->getResult();
    }
}
