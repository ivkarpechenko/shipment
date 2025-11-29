<?php

namespace App\Tests\Domain\Contact\Service;

use App\Domain\Contact\Entity\Contact;
use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Domain\Contact\Service\UpdateContactService;
use App\Domain\Contact\Service\Validator\PhoneNumberValidator;
use App\Tests\Fixture\Contact\ContactFixture;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UpdateContactServiceTest extends KernelTestCase
{
    protected ContactRepositoryInterface $repositoryMock;

    protected PhoneNumberValidator $phoneNumberValidator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = $this->createMock(ContactRepositoryInterface::class);
        $this->phoneNumberValidator = $this->getContainer()->get(PhoneNumberValidator::class);
    }

    public function testUpdateContactName()
    {
        $service = new UpdateContactService($this->repositoryMock, $this->phoneNumberValidator);

        $this->repositoryMock->method('ofEmail')->willReturn(ContactFixture::getOne(
            'test@gmail.com',
            'test',
            []
        ));

        $service->update('test@gmail.com', 'updated test');

        $this->repositoryMock->method('ofEmail')->willReturn(ContactFixture::getOne(
            'test@gmail.com',
            'test updated test',
            []
        ));

        $contact = $this->repositoryMock->ofEmail('test@gmail.com');

        $this->assertNotNull($contact);
        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertEquals('test@gmail.com', $contact->getEmail());
        $this->assertEquals('updated test', $contact->getName());
        $this->assertInstanceOf(Collection::class, $contact->getPhones());
        $this->assertEmpty($contact->getPhones());
        $this->assertCount(0, $contact->getPhones());
        $this->assertNotNull($contact->getCreatedAt());
        $this->assertNotNull($contact->getUpdatedAt());
    }

    public function testAddNewPhoneNumberToContact()
    {
        $service = new UpdateContactService($this->repositoryMock, $this->phoneNumberValidator);

        $this->repositoryMock->method('ofEmail')->willReturn(ContactFixture::getOne(
            'test@gmail.com',
            'test',
            [
                '+44444444444',
                '+55555555555',
                '+66666666666',
            ]
        ));

        $contact = $this->repositoryMock->ofEmail('test@gmail.com');

        $this->assertInstanceOf(Collection::class, $contact->getPhones());
        $this->assertNotEmpty($contact->getPhones());
        $this->assertCount(3, $contact->getPhones());

        $service->update('test@gmail.com', phones: [
            '+77777777777',
        ]);

        $this->repositoryMock->method('ofEmail')->willReturn(ContactFixture::getOne(
            'test@gmail.com',
            'test',
            [
                '+77777777777',
            ]
        ));

        $contact = $this->repositoryMock->ofEmail('test@gmail.com');

        $this->assertNotNull($contact);
        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertEquals('test@gmail.com', $contact->getEmail());
        $this->assertEquals('test', $contact->getName());
        $this->assertInstanceOf(Collection::class, $contact->getPhones());
        $this->assertNotEmpty($contact->getPhones());
        $this->assertCount(1, $contact->getPhones());
        $this->assertNotNull($contact->getCreatedAt());
        $this->assertNotNull($contact->getUpdatedAt());
    }
}
