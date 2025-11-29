<?php

namespace App\Tests\Application\Contact\Query;

use App\Application\Contact\Query\FindContactByEmailQuery;
use App\Application\Contact\Query\FindContactByEmailQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Domain\Contact\Entity\Contact;
use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Tests\Fixture\Contact\ContactFixture;
use App\Tests\MessageBusTestCase;
use Doctrine\Common\Collections\Collection;

class FindContactByEmailQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindContactByEmailQuery('test@gmail.com')
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindContactByEmailQueryHandler::class)
        );
    }

    public function testFindContactByEmailQueryHandler()
    {
        $container = $this->getContainer();
        $repository = $container->get(ContactRepositoryInterface::class);

        $newContact = ContactFixture::getOne('test@gmail.com', 'test', []);
        $repository->create($newContact);

        $contact = $container->get(FindContactByEmailQueryHandler::class)(
            new FindContactByEmailQuery($newContact->getEmail())
        );

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
