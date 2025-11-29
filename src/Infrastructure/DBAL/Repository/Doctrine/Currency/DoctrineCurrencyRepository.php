<?php

namespace App\Infrastructure\DBAL\Repository\Doctrine\Currency;

use App\Domain\Currency\Entity\Currency;
use App\Domain\Currency\Repository\CurrencyRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Uid\Uuid;

readonly class DoctrineCurrencyRepository implements CurrencyRepositoryInterface
{
    public function __construct(public EntityManagerInterface $entityManager)
    {
    }

    public function create(Currency $currency): void
    {
        $this->entityManager->persist($currency);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function update(Currency $currency): void
    {
        $this->entityManager->persist($currency);
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function ofId(Uuid $currencyId): ?Currency
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Currency::class, 'currency')
            ->select('currency')
            ->where('currency.id = :id')
            ->andWhere('currency.isActive = true')
            ->setParameter('id', $currencyId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofIdDeactivated(Uuid $currencyId): ?Currency
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Currency::class, 'currency')
            ->select('currency')
            ->where('currency.id = :id')
            ->andWhere('currency.isActive = false')
            ->setParameter('id', $currencyId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofCode(string $code): ?Currency
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Currency::class, 'currency')
            ->select('currency')
            ->where('currency.code = :code')
            ->andWhere('currency.isActive = true')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofCodeDeactivated(string $code): ?Currency
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Currency::class, 'currency')
            ->select('currency')
            ->where('currency.code = :code')
            ->andWhere('currency.isActive = false')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofNum(int $num): ?Currency
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Currency::class, 'currency')
            ->select('currency')
            ->where('currency.num = :num')
            ->andWhere('currency.isActive = true')
            ->setParameter('num', $num)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofNumDeactivated(int $num): ?Currency
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Currency::class, 'currency')
            ->select('currency')
            ->where('currency.num = :num')
            ->andWhere('currency.isActive = false')
            ->setParameter('num', $num)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function all(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Currency::class, 'currency')
            ->select('currency')
            ->getQuery()
            ->getResult();
    }

    public function paginate(int $page, int $offset): array
    {
        $query = $this->entityManager
            ->createQueryBuilder()
            ->from(Currency::class, 'currency')
            ->select('currency')
            ->andWhere('currency.isActive = true')
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
}
