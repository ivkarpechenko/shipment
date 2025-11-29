<?php

namespace App\Tests\Infrastructure\DaData\Service;

use App\Domain\Address\Service\Dto\AddressDto;
use App\Infrastructure\DaData\Service\FindByAddressService;
use App\Tests\Fixture\DaData\DaDataAddressDtoFixture;
use App\Tests\Fixture\DaData\DaDataResponseFixture;
use Dadata\DadataClient;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FindByAddressServiceTest extends KernelTestCase
{
    public function testFindByAddress()
    {
        $service = $this->createMock(FindByAddressService::class);
        $service->method('find')->willReturn(DaDataAddressDtoFixture::getOne());

        $response = $service->find('Белгородская обл, г Алексеевка, ул Слободская, д 1/1');

        $this->assertNotNull($response);
        $this->assertInstanceOf(AddressDto::class, $response);
    }

    public function testIfEmptySuggestFindFromClean()
    {
        $container = $this->getContainer();
        $daDataCleanMock = $this->createMock(DadataClient::class);
        $daDataCleanMock
            ->method('suggest')
            ->willReturn(null);
        $daDataCleanMock
            ->method('clean')
            ->willReturn(DaDataResponseFixture::getCleanResponse());

        $serializer = $container->get('serializer');
        $service = new FindByAddressService($daDataCleanMock, $serializer);
        $response = $service->find('г Москва, ул Сухонская, д 11, кв 89');

        $this->assertNotNull($response);
        $this->assertInstanceOf(AddressDto::class, $response);
    }

    public function testIfEmptyCleanFindFromSuggest()
    {
        $container = $this->getContainer();
        $daDataCleanMock = $this->createMock(DadataClient::class);
        $daDataCleanMock
            ->method('suggest')
            ->willReturn(DaDataResponseFixture::getSuggestResponse());
        $daDataCleanMock
            ->method('clean')
            ->willReturn(null);

        $serializer = $container->get('serializer');
        $service = new FindByAddressService($daDataCleanMock, $serializer);
        $response = $service->find('г Москва, р-н Гольяново, ул Хабаровская');

        $this->assertNotNull($response);
        $this->assertInstanceOf(AddressDto::class, $response);
    }
}
