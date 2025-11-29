<?php

namespace App\Application\Shipment\Command\Dto;

class ContactDto
{
    public function __construct(
        public string $email,
        public string $name,
        /** @var string[] $phones */
        public array $phones
    ) {
    }

    public static function fromArray(array $contact): self
    {
        return new self($contact['email'], $contact['name'], $contact['phones']);
    }
}
