<?php

namespace App\Infrastructure\DBAL\Repository\Doctrine\Country;

use App\Domain\Country\Entity\Country;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Uid\Uuid;

readonly class DoctrineCountryRepository implements CountryRepositoryInterface
{
    public function __construct(public EntityManagerInterface $entityManager)
    {
    }

    public function create(Country $country): void
    {
        $this->entityManager->persist($country);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function update(Country $country): void
    {
        $this->entityManager->persist($country);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function delete(Country $country): void
    {
        $this->entityManager->persist($country);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function restore(Country $country): void
    {
        $this->entityManager->persist($country);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function all(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Country::class, 'country')
            ->select('country')
            ->getQuery()
            ->getResult();
    }

    public function paginate(int $page, int $offset): array
    {
        $query = $this->entityManager
            ->createQueryBuilder()
            ->from(Country::class, 'country')
            ->select('country')
            ->andWhere('country.deletedAt IS NULL')
            ->andWhere('country.isActive = true')
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

    public function ofId(Uuid $countryId): ?Country
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Country::class, 'country')
            ->select('country')
            ->where('country.id = :id')
            ->andWhere('country.deletedAt IS NULL')
            ->andWhere('country.isActive = true')
            ->setParameter('id', $countryId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofIdDeleted(Uuid $countryId): ?Country
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Country::class, 'country')
            ->select('country')
            ->where('country.id = :id')
            ->andWhere('country.deletedAt IS NOT NULL')
            ->setParameter('id', $countryId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofIdDeactivated(Uuid $countryId): ?Country
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Country::class, 'country')
            ->select('country')
            ->where('country.id = :id')
            ->andWhere('country.isActive = false')
            ->setParameter('id', $countryId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofCode(string $code): ?Country
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Country::class, 'country')
            ->select('country')
            ->where('country.code = :code')
            ->andWhere('country.deletedAt IS NULL')
            ->andWhere('country.isActive = true')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofCodeDeactivated(string $code): ?Country
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Country::class, 'country')
            ->select('country')
            ->where('country.code = :code')
            ->andWhere('country.isActive = false')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofCodeDeleted(string $code): ?Country
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Country::class, 'country')
            ->select('country')
            ->where('country.code = :code')
            ->andWhere('country.deletedAt IS NOT NULL')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofName(string $name): ?Country
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Country::class, 'country')
            ->select('country')
            ->where('country.name = :name')
            ->andWhere('country.deletedAt IS NULL')
            ->andWhere('country.isActive = true')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
