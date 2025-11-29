<?php

namespace App\Tests\Infrastructure\DBAL\Repository\Doctrine\Contact;

use App\Domain\Contact\Entity\Contact;
use App\Infrastructure\DBAL\Repository\Doctrine\Contact\DoctrineContactRepository;
use App\Tests\DoctrineTestCase;
use App\Tests\Fixture\Contact\ContactFixture;
use App\Tests\Fixture\Contact\PhoneFixture;
use Doctrine\Common\Collections\Collection;

class DoctrineContactRepositoryTest extends DoctrineTestCase
{
    private DoctrineContactRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->getContainer()->get(DoctrineContactRepository::class);
    }

    public function testCreateContact()
    {
        $this->assertEmpty($this->repository->all());

        $newContact = ContactFixture::getOne('test@gmail.com', 'test', []);

        $this->repository->create($newContact);

        $contact = $this->repository->ofId($newContact->getId());

        $this->assertNotNull($contact);
        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertEquals('test@gmail.com', $contact->getEmail());
        $this->assertEquals('test', $contact->getName());
        $this->assertInstanceOf(Collection::class, $contact->getPhones());
        $this->assertEmpty($contact->getPhones());
        $this->assertCount(0, $contact->getPhones());
        $this->assertNotNull($contact->getCreatedAt());
        $this->assertNull($contact->getUpdatedAt());
    }

    public function testUpdateContact()
    {
        $this->assertEmpty($this->repository->all());

        $this->repository->create(ContactFixture::getOne('test@gmail.com', 'test', []));

        $contact = $this->repository->ofEmail('test@gmail.com');

        $this->assertEquals('test', $contact->getName());

        // Update name
        $contact->changeName('updated test');
        $this->repository->update($contact);

        $updatedContact = $this->repository->ofId($contact->getId());

        $this->assertNotNull($updatedContact->getUpdatedAt());
        $this->assertEquals('updated test', $updatedContact->getName());

        $this->assertEmpty($updatedContact->getPhones());
        $this->assertInstanceOf(Collection::class, $updatedContact->getPhones());
        $this->assertCount(0, $updatedContact->getPhones());

        // Add phone
        $updatedContact->addPhone(PhoneFixture::getOne('+7777777777'));
        $this->repository->update($updatedContact);

        $updatedContactWithPhone = $this->repository->ofId($contact->getId());

        $this->assertNotNull($updatedContactWithPhone);
        $this->assertNotEmpty($updatedContactWithPhone->getPhones());
        $this->assertInstanceOf(Collection::class, $updatedContactWithPhone->getPhones());
        $this->assertCount(1, $updatedContactWithPhone->getPhones());

        // Remove phone
        $updatedContactWithPhone->removePhone($updatedContactWithPhone->getPhones()->first());
        $this->repository->update($updatedContactWithPhone);

        $updatedContactWithoutPhone = $this->repository->ofId($contact->getId());

        $this->assertNotNull($updatedContactWithoutPhone);
        $this->assertEmpty($updatedContactWithoutPhone->getPhones());
        $this->assertInstanceOf(Collection::class, $updatedContactWithoutPhone->getPhones());
        $this->assertCount(0, $updatedContactWithoutPhone->getPhones());
    }

    public function testOfId()
    {
        $this->assertEmpty($this->repository->all());

        $newContact = ContactFixture::getOne('test@gmail.com', 'test', []);
        $this->repository->create($newContact);

        $contact = $this->repository->ofId($newContact->getId());

        $this->assertNotNull($contact);
        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertEquals('test@gmail.com', $contact->getEmail());
        $this->assertEquals('test', $contact->getName());
        $this->assertInstanceOf(Collection::class, $contact->getPhones());
        $this->assertEmpty($contact->getPhones());
        $this->assertCount(0, $contact->getPhones());
        $this->assertNotNull($contact->getCreatedAt());
        $this->assertNull($contact->getUpdatedAt());
    }

    public function testOfEmail()
    {
        $this->assertEmpty($this->repository->all());

        $newContact = ContactFixture::getOne('test@gmail.com', 'test', []);
        $this->repository->create($newContact);

        $contact = $this->repository->ofEmail($newContact->getEmail());

        $this->assertNotNull($contact);
        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertEquals('test@gmail.com', $contact->getEmail());
        $this->assertEquals('test', $contact->getName());
        $this->assertInstanceOf(Collection::class, $contact->getPhones());
        $this->assertEmpty($contact->getPhones());
        $this->assertCount(0, $contact->getPhones());
        $this->assertNotNull($contact->getCreatedAt());
        $this->assertNull($contact->getUpdatedAt());
    }
}
