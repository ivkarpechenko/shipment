<?php

namespace App\Infrastructure\DBAL\Repository\Doctrine\Contact;

use App\Domain\Contact\Entity\Contact;
use App\Domain\Contact\Repository\ContactRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Uid\Uuid;

readonly class DoctrineContactRepository implements ContactRepositoryInterface
{
    public function __construct(public EntityManagerInterface $entityManager)
    {
    }

    public function create(Contact $contact): void
    {
        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        $this->entityManager->clear();
    }

    public function update(Contact $contact): void
    {
        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        $this->entityManager->clear();
    }

    public function all(): array
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Contact::class, 'contact')
            ->select('contact')
            ->getQuery()
            ->getResult();
    }

    public function paginate(int $page, int $offset): array
    {
        $query = $this->entityManager
            ->createQueryBuilder()
            ->from(Contact::class, 'contact')
            ->select('contact')
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

    public function ofId(Uuid $contactId): ?Contact
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Contact::class, 'contact')
            ->select('contact')
            ->where('contact.id = :id')
            ->setParameter('id', $contactId, 'uuid')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function ofEmail(string $email): ?Contact
    {
        return $this->entityManager
            ->createQueryBuilder()
            ->from(Contact::class, 'contact')
            ->select('contact')
            ->where('contact.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
