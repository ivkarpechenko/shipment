<?php

namespace App\Domain\Contact\Repository;

use App\Domain\Contact\Entity\Contact;
use Symfony\Component\Uid\Uuid;

interface ContactRepositoryInterface
{
    public function create(Contact $contact): void;

    public function update(Contact $contact): void;

    public function all(): array;

    public function paginate(int $page, int $offset): array;

    public function ofId(Uuid $contactId): ?Contact;

    public function ofEmail(string $email): ?Contact;
}
