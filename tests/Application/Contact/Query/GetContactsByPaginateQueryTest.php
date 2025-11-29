<?php

namespace App\Tests\Application\Contact\Query;

use App\Application\Contact\Query\GetContactsByPaginateQuery;
use App\Application\Contact\Query\GetContactsByPaginateQueryHandler;
use App\Application\Query;
use App\Application\QueryHandler;
use App\Domain\Contact\Entity\Contact;
use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Tests\Fixture\Contact\ContactFixture;
use App\Tests\MessageBusTestCase;

class GetContactsByPaginateQueryTest extends MessageBusTestCase
{
    public function testQueryInstanceOf()
    {
        $this->assertInstanceOf(
            Query::class,
            new GetContactsByPaginateQuery(1, 1)
        );
        $this->assertInstanceOf(
            QueryHandler::class,
            $this->getContainer()->get(GetContactsByPaginateQueryHandler::class)
        );
    }

    public function testGetContactsByPaginateQueryHandler()
    {
        $container = $this->getContainer();
        $newContact = ContactFixture::getOne('test@gmail.com', 'test', []);
        $newContact2 = ContactFixture::getOne('test2@gmail.com', 'test2', []);
        $repository = $container->get(ContactRepositoryInterface::class);

        $repository->create($newContact);
        $repository->create($newContact2);

        $contacts = $container->get(GetContactsByPaginateQueryHandler::class)(
            new GetContactsByPaginateQuery(0, 2)
        );

        $this->assertNotEmpty($contacts);
        $this->assertIsArray($contacts);
        $this->assertArrayHasKey('data', $contacts);
        $this->assertArrayHasKey('total', $contacts);
        $this->assertArrayHasKey('pages', $contacts);

        $contact = reset($contacts['data']);

        $this->assertInstanceOf(Contact::class, $contact);
        $this->assertEquals('test@gmail.com', $contact->getEmail());
        $this->assertEquals('test', $contact->getName());
        $this->assertNotNull($contact->getCreatedAt());
        $this->assertNull($contact->getUpdatedAt());

        $this->assertEquals(2, $contacts['total']);
        $this->assertEquals(1, $contacts['pages']);
    }
}
