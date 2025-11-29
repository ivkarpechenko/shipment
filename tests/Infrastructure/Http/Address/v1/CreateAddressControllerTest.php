<?php

namespace App\Tests\Infrastructure\Http\Address\v1;

use App\Application\Address\Query\External\FindExternalAddressInterface;
use App\Domain\Address\Repository\AddressRepositoryInterface;
use App\Domain\City\Entity\City;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\DaData\DaDataAddressDtoFixture;
use App\Tests\HttpTestCase;
use Symfony\Component\Uid\Uuid;

class CreateAddressControllerTest extends HttpTestCase
{
    public function testCreateAddressRoute()
    {
        $container = $this->getContainer();

        $countryRepository = $container->get(CountryRepositoryInterface::class);
        $addressRepository = $container->get(AddressRepositoryInterface::class);

        $this->assertEmpty($addressRepository->all());

        $country = CountryFixture::getOne('Россия', 'RU', Uuid::v1());
        $countryRepository->create($country);

        $service = $this->createMock(FindExternalAddressInterface::class);
        $service->method('find')->willReturn(DaDataAddressDtoFixture::getOne());
        $container->set(FindExternalAddressInterface::class, $service);

        $this->client->request(
            'POST',
            '/api/v1/address',
            [
                'address' => '309850, Белгородская обл, Алексеевский р-н, г Алексеевка, ул Слободская, д 1/1',
            ]
        );

        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(201);

        $address = $addressRepository->ofAddress('309850, Белгородская обл, Алексеевский р-н, г Алексеевка, ул Слободская, д 1/1');

        $this->assertNotNull($address->getCity());
        $this->assertInstanceOf(City::class, $address->getCity());
        $this->assertEquals('309850', $address->getPostalCode());
        $this->assertEquals('ул Слободская', $address->getStreet());
        $this->assertEquals('1/1', $address->getHouse());
        $this->assertTrue($address->isActive());
        $this->assertNotNull($address->getCreatedAt());
    }
}
