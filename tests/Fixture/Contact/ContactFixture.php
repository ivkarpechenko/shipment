<?php

namespace App\Tests\Fixture\Contact;

use App\Domain\Contact\Entity\Contact;
use App\Domain\Contact\Entity\Phone;
use Symfony\Component\Uid\Uuid;

class ContactFixture
{
    public static function getOne(string $email, string $name, array $phones = [], ?Uuid $id = null): Contact
    {
        $contact = new Contact($email, $name);

        foreach ($phones as $phone) {
            $contact->addPhone(new Phone($phone));
        }

        $reflectionClass = new \ReflectionClass(Contact::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($contact, $id ?: Uuid::v1());

        return $contact;
    }
}
