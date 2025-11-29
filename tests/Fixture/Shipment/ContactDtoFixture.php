<?php

namespace App\Tests\Fixture\Shipment;

use App\Application\Shipment\Command\Dto\ContactDto;

class ContactDtoFixture
{
    public static function getOne(string $email, string $name, array $phones = []): ContactDto
    {
        return new ContactDto($email, $name, $phones);
    }
}
