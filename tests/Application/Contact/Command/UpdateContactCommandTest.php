<?php

namespace App\Tests\Application\Contact\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Contact\Command\UpdateContactCommand;
use App\Application\Contact\Command\UpdateContactCommandHandler;
use App\Domain\Contact\Entity\Contact;
use App\Domain\Contact\Exception\ContactNotFoundException;
use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Tests\Fixture\Contact\ContactFixture;
use App\Tests\MessageBusTestCase;
use Doctrine\Common\Collections\Collection;

class UpdateContactCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new UpdateContactCommand('test@gmail.com', 'test', [])
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(UpdateContactCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new UpdateContactCommand('test@gmail.com', 'test', []);
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testUpdateContactName()
    {
        $container = $this->getContainer();
        $repository = $container->get(ContactRepositoryInterface::class);

        $newContact = ContactFixture::getOne('test@gmail.com', 'test', []);
        $repository->create($newContact);

        $container->get(UpdateContactCommandHandler::class)(
            new UpdateContactCommand($newContact->getEmail(), 'updated test')
        );

        $contact = $repository->ofEmail($newContact->getEmail());

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

    public function testAddPhoneToContact()
    {
        $container = $this->getContainer();
        $repository = $container->get(ContactRepositoryInterface::class);

        $newContact = ContactFixture::getOne('test@gmail.com', 'test', []);
        $repository->create($newContact);

        $contact = $repository->ofEmail($newContact->getEmail());
        $this->assertInstanceOf(Collection::class, $contact->getPhones());
        $this->assertEmpty($contact->getPhones());

        $container->get(UpdateContactCommandHandler::class)(
            new UpdateContactCommand($newContact->getEmail(), phones: [
                '+77777777777',
            ])
        );

        $contact = $repository->ofEmail($newContact->getEmail());

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

    public function testRemovePhoneFromContact()
    {
        $container = $this->getContainer();
        $repository = $container->get(ContactRepositoryInterface::class);

        $newContact = ContactFixture::getOne('test@gmail.com', 'test', [
            '+77777777777', '+78888888888',
        ]);
        $repository->create($newContact);

        $contact = $repository->ofEmail($newContact->getEmail());
        $this->assertInstanceOf(Collection::class, $contact->getPhones());
        $this->assertNotEmpty($contact->getPhones());
        $this->assertCount(2, $contact->getPhones());

        $container->get(UpdateContactCommandHandler::class)(
            new UpdateContactCommand($newContact->getEmail(), phones: [
                '+77777777777',
            ])
        );

        $contact = $repository->ofEmail($newContact->getEmail());

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

    public function testUpdateContactIfNotExist()
    {
        $container = $this->getContainer();

        $this->expectException(ContactNotFoundException::class);
        $container->get(UpdateContactCommandHandler::class)(
            new UpdateContactCommand('test@gmail.com', phones: [
                '+77777777777',
            ])
        );
    }
}
