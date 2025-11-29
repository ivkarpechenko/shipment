<?php

namespace App\Tests\Infrastructure\Http\Contact\v1;

use App\Domain\Contact\Repository\ContactRepositoryInterface;
use App\Tests\Fixture\Contact\ContactFixture;
use App\Tests\HttpTestCase;

class FindContactControllerTest extends HttpTestCase
{
    public function testFindById()
    {
        $newContact = ContactFixture::getOne('test@gmail.com', 'test', [
            '+7777777777',
        ]);
        $repository = $this->getContainer()->get(ContactRepositoryInterface::class);
        $repository->create($newContact);

        $this->client->request(
            'GET',
            "/api/v1/contact/find-by-id/{$newContact->getId()->toRfc4122()}"
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

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

    public function testFindByEmail()
    {
        $newContact = ContactFixture::getOne('test@gmail.com', 'test', [
            '+7777777777',
        ]);
        $repository = $this->getContainer()->get(ContactRepositoryInterface::class);
        $repository->create($newContact);

        $this->client->request(
            'GET',
            "/api/v1/contact/find-by-email/{$newContact->getEmail()}"
        );

        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/json');

        $response = $this->client->getResponse()->getContent();

        $this->assertNotEmpty($response);

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
