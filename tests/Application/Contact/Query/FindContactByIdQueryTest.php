<?php

namespace App\Tests\Application\Contact\Query;

use App\Application\Contact\Query\FindContactByIdQuery;
use App\Application\Contact\Query\FindContactByIdQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Domain\Contact\Entity\Contact;
use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Tests\Fixture\Contact\ContactFixture;
use App\Tests\MessageBusTestCase;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;

class FindContactByIdQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new FindContactByIdQuery(Uuid::v1())
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(FindContactByIdQueryHandler::class)
        );
    }

    public function testFindContactByIdQueryHandler()
    {
        $container = $this->getContainer();
        $repository = $container->get(ContactRepositoryInterface::class);

        $newContact = ContactFixture::getOne('test@gmail.com', 'test', []);
        $repository->create($newContact);

        $contact = $container->get(FindContactByIdQueryHandler::class)(
            new FindContactByIdQuery($newContact->getId())
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
