<?php

namespace App\Tests\Fixture\Contact;

use App\Domain\Contact\Entity\Phone;
use Symfony\Component\Uid\Uuid;

class PhoneFixture
{
    public static function getOne(string $number, ?Uuid $id = null): Phone
    {
        $phone = new Phone($number);

        $reflectionClass = new \ReflectionClass(Phone::class);
        $reflectionProperty = $reflectionClass->getProperty('id');
        $reflectionProperty->setValue($phone, $id ?: Uuid::v1());

        return $phone;
    }
}
