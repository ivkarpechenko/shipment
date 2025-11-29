<?php

namespace App\Tests\Domain\Contact\Entity;

use App\Domain\Contact\Entity\Contact;
use App\Domain\Contact\Entity\Phone;
use App\Tests\Fixture\Contact\ContactFixture;
use App\Tests\Fixture\Contact\PhoneFixture;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;

class ContactTest extends KernelTestCase
{
    public function testCreateContact()
    {
        $contact = ContactFixture::getOne('test@gmail.com', 'test', [
            '+77777777777',
            '+78888888888',
        ]);

        $this->assertNotNull($contact);
        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertInstanceOf(Uuid::class, $contact->getId());
        $this->assertEquals('test@gmail.com', $contact->getEmail());
        $this->assertEquals('test', $contact->getName());
        $this->assertNotNull($contact->getCreatedAt());
        $this->assertNotNull($contact->getUpdatedAt());
        $this->assertNotEmpty($contact->getPhones());
        $this->assertInstanceOf(Collection::class, $contact->getPhones());
        $this->assertCount(2, $contact->getPhones());
        $this->assertInstanceOf(Phone::class, $contact->getPhones()->first());
        $this->assertEquals('+77777777777', $contact->getPhones()->first()->getNumber());
        $this->assertEquals('+78888888888', $contact->getPhones()->last()->getNumber());
    }

    public function testUpdateContactName()
    {
        $contact = ContactFixture::getOne('test@gmail.com', 'test', []);

        $this->assertNotNull($contact);
        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertInstanceOf(Uuid::class, $contact->getId());
        $this->assertEquals('test@gmail.com', $contact->getEmail());
        $this->assertEquals('test', $contact->getName());
        $this->assertNotNull($contact->getCreatedAt());
        $this->assertNull($contact->getUpdatedAt());

        $contact->changeName('updated test');

        $this->assertEquals('updated test', $contact->getName());
        $this->assertNotNull($contact->getUpdatedAt());
    }

    public function testAddPhoneToContact()
    {
        $contact = ContactFixture::getOne('test@gmail.com', 'test', []);

        $this->assertNotNull($contact);
        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertInstanceOf(Uuid::class, $contact->getId());
        $this->assertEquals('test@gmail.com', $contact->getEmail());
        $this->assertEquals('test', $contact->getName());
        $this->assertEmpty($contact->getPhones());
        $this->assertNotNull($contact->getCreatedAt());
        $this->assertNull($contact->getUpdatedAt());

        $phone = PhoneFixture::getOne('+77777777777');
        $contact->addPhone($phone);

        $this->assertInstanceOf(Collection::class, $contact->getPhones());
        $this->assertCount(1, $contact->getPhones());
        $this->assertInstanceOf(Phone::class, $contact->getPhones()->first());
        $this->assertEquals($phone, $contact->getPhones()->first());

        $this->assertNotNull($contact->getUpdatedAt());
    }

    public function testRemovePhoneFromContact()
    {
        $contact = ContactFixture::getOne('test@gmail.com', 'test', [
            '+77777777777',
        ]);

        $this->assertNotNull($contact);
        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertInstanceOf(Uuid::class, $contact->getId());
        $this->assertEquals('test@gmail.com', $contact->getEmail());
        $this->assertEquals('test', $contact->getName());
        $this->assertNotEmpty($contact->getPhones());
        $this->assertInstanceOf(Collection::class, $contact->getPhones());
        $this->assertCount(1, $contact->getPhones());
        $this->assertInstanceOf(Phone::class, $contact->getPhones()->first());
        $this->assertEquals('+77777777777', $contact->getPhones()->first()->getNumber());
        $this->assertNotNull($contact->getCreatedAt());
        $this->assertNotNull($contact->getUpdatedAt());

        $contact->removePhone($contact->getPhones()->first());

        $this->assertInstanceOf(Collection::class, $contact->getPhones());
        $this->assertCount(0, $contact->getPhones());
        $this->assertEmpty($contact->getPhones());
        $this->assertNotNull($contact->getUpdatedAt());
    }
}
