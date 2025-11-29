<?php

namespace App\Tests\Application\Contact\Command;

use App\Application\Command;
use App\Application\CommandHandler;
use App\Application\Contact\Command\CreateContactCommand;
use App\Application\Contact\Command\CreateContactCommandHandler;
use App\Domain\Contact\Entity\Contact;
use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Tests\MessageBusTestCase;
use Doctrine\Common\Collections\Collection;

class CreateContactCommandTest extends MessageBusTestCase
{
    public function testCommandInstanceOf()
    {
        $this->assertInstanceOf(
            Command::class,
            new CreateContactCommand('test@gmail.com', 'test', [])
        );
        $this->assertInstanceOf(
            CommandHandler::class,
            $this->getContainer()->get(CreateContactCommandHandler::class)
        );
    }

    public function testCommandDispatch()
    {
        $command = new CreateContactCommand('test@gmail.com', 'test', []);
        $this->commandBus->dispatch($command);

        $this->assertSame($command, $this->messageBus->lastDispatchedCommand());
    }

    public function testCreateContactCommandHandler()
    {
        $container = $this->getContainer();
        $container->get(CreateContactCommandHandler::class)(
            new CreateContactCommand('test@gmail.com', 'test', [
                '+77777777777',
            ])
        );

        $contact = $container->get(ContactRepositoryInterface::class)->ofEmail('test@gmail.com');

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
}
