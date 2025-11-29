<?php

namespace App\Tests\Infrastructure\Http\Contact\v1;

use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Tests\Fixture\Contact\ContactFixture;
use App\Tests\HttpTestCase;

class ContactListControllerTest extends HttpTestCase
{
    public function testAllByPaginateRoute()
    {
        $newContact = ContactFixture::getOne('test@gmail.com', 'test', [
            '+7777777777',
        ]);
        $repository = $this->getContainer()->get(ContactRepositoryInterface::class);
        $repository->create($newContact);

        $this->client->request('GET', '/api/v1/contact/paginate?page=0&offset=1');

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

        $this->assertStringContainsString('data', $response);
        $this->assertStringContainsString('total', $response);
        $this->assertStringContainsString('pages', $response);

        $this->assertStringContainsString('id', $response);
        $this->assertStringContainsString('email', $response);
        $this->assertStringContainsString('name', $response);
        $this->assertStringContainsString('phones', $response);
        $this->assertStringContainsString('createdAt', $response);
        $this->assertStringContainsString('updatedAt', $response);

        $this->assertStringContainsString('test@gmail.com', $response);
        $this->assertStringContainsString('test', $response);
        $this->assertStringContainsString('+7777777777', $response);
    }
}
