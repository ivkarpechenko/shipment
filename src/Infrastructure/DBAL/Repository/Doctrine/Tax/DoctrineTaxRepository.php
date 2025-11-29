<?php

namespace App\Infrastructure\DBAL\Repository\Doctrine\Tax;

use App\Domain\Country\Entity\Country;
use App\Domain\Tax\Entity\Tax;
use App\Domain\Tax\Repository\TaxRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Uid\Uuid;

class DoctrineTaxRepository implements TaxRepositoryInterface
{
    public function __construct(public EntityManagerInterface $entityManager)
    {
    }

    public function create(Tax $tax): void
    {
        $this->entityManager->persist($tax);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function update(Tax $tax): void
    {
        $this->entityManager->persist($tax);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function delete(Tax $tax): void
    {
        $this->entityManager->persist($tax);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function restore(Tax $tax): void
    {
        $this->entityManager->persist($tax);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function all(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Tax::class, 'tax')
            ->select('tax')
            ->getQuery()
            ->getResult();
    }

    public function paginate(int $page, int $offset): array
    {
        $query = $this->entityManager
            ->createQueryBuilder()
            ->from(Tax::class, 'tax')
            ->select('tax')
            ->andWhere('tax.deletedAt IS NULL')
            ->getQuery();

        $paginator = new Paginator($query);
        $total = count($paginator);
        $pages = (int) ceil($total / $offset);

        $taxs = $paginator
            ->getQuery()
            ->setFirstResult($page)
            ->setMaxResults($offset)
            ->getResult();

        return [
            'data' => $taxs,
            'total' => $total,
            'pages' => $pages,
        ];
    }

    public function ofId(Uuid $taxId): ?Tax
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Tax::class, 'tax')
            ->select('tax')
            ->where('tax.id = :id')
            ->andWhere('tax.deletedAt IS NULL')
            ->setParameter('id', $taxId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofCountry(Country $country): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Tax::class, 'tax')
            ->select('tax')
            ->where('tax.country = :country')
            ->andWhere('tax.deletedAt IS NULL')
            ->setParameter('country', $country->getId(), 'uuid')
            ->getQuery()
            ->getResult();
    }

    public function ofIdDeleted(Uuid $taxId): ?Tax
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Tax::class, 'tax')
            ->select('tax')
            ->where('tax.id = :id')
            ->andWhere('tax.deletedAt IS NOT NULL')
            ->setParameter('id', $taxId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofCountryAndName(Country $country, string $name): ?Tax
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Tax::class, 'tax')
            ->select('tax')
            ->where('tax.name = :name')
            ->andWhere('tax.country = :country')
            ->andWhere('tax.deletedAt IS NULL')
            ->setParameter('name', $name)
            ->setParameter('country', $country->getId(), 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofCountryAndNameDeleted(Country $country, string $name): ?Tax
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Tax::class, 'tax')
            ->select('tax')
            ->where('tax.name = :name')
            ->andWhere('tax.country = :country')
            ->andWhere('tax.deletedAt IS NOT NULL')
            ->setParameter('name', $name)
            ->setParameter('country', $country->getId(), 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
