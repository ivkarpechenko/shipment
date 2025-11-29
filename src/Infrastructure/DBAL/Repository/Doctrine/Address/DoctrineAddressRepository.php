<?php

namespace App\Infrastructure\DBAL\Repository\Doctrine\Address;

use App\Domain\Address\Entity\Address;
use App\Domain\Address\Repository\AddressRepositoryInterface;
use App\Domain\City\Entity\City;
use App\Domain\Country\Entity\Country;
use App\Domain\Region\Entity\Region;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Uid\Uuid;

class DoctrineAddressRepository implements AddressRepositoryInterface
{
    public function __construct(public EntityManagerInterface $entityManager)
    {
    }

    public function create(Address $address): void
    {
        $this->entityManager->persist($address);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function update(Address $address): void
    {
        $this->entityManager->persist($address);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function delete(Address $address): void
    {
        $this->entityManager->persist($address);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function restore(Address $address): void
    {
        $this->entityManager->persist($address);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function all(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Address::class, 'address')
            ->select('address')
            ->getQuery()
            ->getResult();
    }

    public function paginate(int $page, int $offset): array
    {
        $query = $this->entityManager
            ->createQueryBuilder()
            ->from(Address::class, 'address')
            ->select('address')
            ->andWhere('address.deletedAt IS NULL')
            ->andWhere('address.isActive = true')
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

    public function ofId(Uuid $addressId): ?Address
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Address::class, 'address')
            ->select('address')
            ->innerJoin(City::class, 'city', Join::WITH, 'address.city = city')
            ->innerJoin(Region::class, 'region', Join::WITH, 'city.region = region')
            ->innerJoin(Country::class, 'country', Join::WITH, 'region.country = country')
            ->where('address.id = :id')
            ->andWhere('address.deletedAt IS NULL')
            ->andWhere('address.isActive = true')
            ->andWhere('city.isActive = true')
            ->andWhere('region.isActive = true')
            ->andWhere('country.isActive = true')
            ->setParameter('id', $addressId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofIdDeleted(Uuid $addressId): ?Address
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Address::class, 'address')
            ->select('address')
            ->where('address.id = :id')
            ->andWhere('address.deletedAt IS NOT NULL')
            ->setParameter('id', $addressId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofIdDeactivated(Uuid $addressId): ?Address
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Address::class, 'address')
            ->select('address')
            ->where('address.id = :id')
            ->andWhere('address.deletedAt IS NULL')
            ->andWhere('address.isActive = false')
            ->setParameter('id', $addressId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofCity(City $city): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Address::class, 'address')
            ->select('address')
            ->innerJoin(City::class, 'city', Join::WITH, 'address.city = city')
            ->innerJoin(Region::class, 'region', Join::WITH, 'city.region = region')
            ->innerJoin(Country::class, 'country', Join::WITH, 'region.country = country')
            ->where('address.city = :city')
            ->andWhere('address.deletedAt IS NULL')
            ->andWhere('address.isActive = true')
            ->andWhere('city.isActive = true')
            ->andWhere('region.isActive = true')
            ->andWhere('country.isActive = true')
            ->setParameter('city', $city->getId(), 'uuid')
            ->getQuery()
            ->getResult();
    }

    public function ofAddress(string $address): ?Address
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Address::class, 'address')
            ->select('address')
            ->innerJoin(City::class, 'city', Join::WITH, 'address.city = city')
            ->innerJoin(Region::class, 'region', Join::WITH, 'city.region = region')
            ->innerJoin(Country::class, 'country', Join::WITH, 'region.country = country')
            ->where('address.address = :address')
            ->andWhere('address.deletedAt IS NULL')
            ->andWhere('address.isActive = true')
            ->andWhere('city.isActive = true')
            ->andWhere('region.isActive = true')
            ->andWhere('country.isActive = true')
            ->setParameter('address', $address)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofAddressDeleted(string $address): ?Address
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Address::class, 'address')
            ->select('address')
            ->where('address.address = :address')
            ->andWhere('address.deletedAt IS NOT NULL')
            ->setParameter('address', $address)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofAddressDeactivated(string $address): ?Address
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Address::class, 'address')
            ->select('address')
            ->where('address.address = :address')
            ->andWhere('address.deletedAt IS NULL')
            ->andWhere('address.isActive = false')
            ->setParameter('address', $address)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
