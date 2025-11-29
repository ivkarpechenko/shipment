<?php

namespace App\Tests\Domain\Contact\Service;

use App\Domain\Contact\Entity\Contact;
use App\Domain\Contact\Exception\ContactAlreadyCreatedException;
use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Domain\Contact\Service\CreateContactService;
use App\Domain\Contact\Service\Validator\EmailValidator;
use App\Domain\Contact\Service\Validator\PhoneNumberValidator;
use App\Tests\Fixture\Contact\ContactFixture;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateContactServiceTest extends KernelTestCase
{
    protected ContactRepositoryInterface $repositoryMock;

    protected PhoneNumberValidator $phoneNumberValidator;

    protected EmailValidator $emailValidator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repositoryMock = $this->createMock(ContactRepositoryInterface::class);
        $this->phoneNumberValidator = $this->getContainer()->get(PhoneNumberValidator::class);
        $this->emailValidator = $this->getContainer()->get(EmailValidator::class);
    }

    public function testCreateContact()
    {
        $service = new CreateContactService(
            $this->repositoryMock,
            $this->phoneNumberValidator,
            $this->emailValidator
        );

        $service->create('test@gmail.com', 'test', [
            '+77777777777',
        ]);

        $this->repositoryMock->method('ofEmail')->willReturn(ContactFixture::getOne(
            'test@gmail.com',
            'test',
            ['+77777777777']
        ));

        $contact = $this->repositoryMock->ofEmail('test@gmail.com');

        $this->assertNotNull($contact);
        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertEquals('test@gmail.com', $contact->getEmail());
        $this->assertEquals('test', $contact->getName());
        $this->assertInstanceOf(Collection::class, $contact->getPhones());
        $this->assertNotEmpty($contact->getPhones());
        $this->assertCount(1, $contact->getPhones());
        $this->assertEquals('+77777777777', $contact->getPhones()->first()->getNumber());
        $this->assertNotNull($contact->getCreatedAt());
        $this->assertNotNull($contact->getUpdatedAt());
    }

    public function testCreateContactWithInvalidPhoneNumber()
    {
        $service = new CreateContactService(
            $this->repositoryMock,
            $this->phoneNumberValidator,
            $this->emailValidator
        );

        $this->expectException(\InvalidArgumentException::class);
        $service->create('test@gmail.com', 'test', [
            '77777777777',
        ]);
    }

    public function testCreateContactIfAlreadyCreated()
    {
        $service = new CreateContactService(
            $this->repositoryMock,
            $this->phoneNumberValidator,
            $this->emailValidator
        );

        $this->repositoryMock->method('ofEmail')->willReturn(ContactFixture::getOne(
            'test@gmail.com',
            'test',
            []
        ));

        $this->expectException(ContactAlreadyCreatedException::class);
        $service->create('test@gmail.com', 'test', [
            '+77777777777',
        ]);
    }
}
