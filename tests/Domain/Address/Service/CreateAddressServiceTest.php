<?php

namespace App\Tests\Domain\Address\Service;

use App\Domain\Address\Exception\AddressAlreadyCreatedException;
use App\Domain\Address\Exception\AddressDeactivatedException;
use App\Domain\Address\Exception\AddressDeletedException;
use App\Domain\Address\Repository\AddressRepositoryInterface;
use App\Domain\Address\Service\CreateAddressService;
use App\Domain\City\Entity\City;
use App\Domain\City\Repository\CityRepositoryInterface;
use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Region\Repository\RegionRepositoryInterface;
use App\Tests\Fixture\Address\AddressFixture;
use App\Tests\Fixture\City\CityFixture;
use App\Tests\Fixture\Country\CountryFixture;
use App\Tests\Fixture\DaData\DaDataAddressDtoFixture;
use App\Tests\Fixture\Region\RegionFixture;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CreateAddressServiceTest extends KernelTestCase
{
    public function testCreateAddress()
    {
        $countryRepositoryMock = $this->createMock(CountryRepositoryInterface::class);
        $regionRepositoryMock = $this->createMock(RegionRepositoryInterface::class);
        $cityRepositoryMock = $this->createMock(CityRepositoryInterface::class);
        $addressRepositoryMock = $this->createMock(AddressRepositoryInterface::class);

        $addressService = new CreateAddressService($addressRepositoryMock, $cityRepositoryMock);

        $countryRepositoryMock->method('ofCode')
            ->willReturn(CountryFixture::getOne('Russia', 'RU'));
        $country = $countryRepositoryMock->ofCode('RU');

        $regionRepositoryMock->method('ofCode')
            ->willReturn(RegionFixture::getOne($country, 'Moskva', 'MOW'));
        $region = $regionRepositoryMock->ofCode('MOW');

        $cityRepositoryMock->method('ofTypeAndName')
            ->willReturn(CityFixture::getOne($region, 'город', 'Алексеевка'));
        $city = $cityRepositoryMock->ofTypeAndName('город', 'Алексеевка');

        $addressDto = DaDataAddressDtoFixture::getOne();

        $addressService->create($addressDto);
        $addressString = '309850, Белгородская обл, Алексеевский р-н, г Алексеевка, ул Слободская, д 1/1';
        $addressRepositoryMock->method('ofAddress')
            ->willReturn(AddressFixture::getOneFromAddressDto($city, $addressDto));
        $address = $addressRepositoryMock->ofAddress($addressString);

        $this->assertNotNull($address->getCity());
        $this->assertInstanceOf(City::class, $address->getCity());
        $this->assertEquals('309850', $address->getPostalCode());
        $this->assertEquals('ул Слободская', $address->getStreet());
        $this->assertEquals('1/1', $address->getHouse());
        $this->assertTrue($address->isActive());
        $this->assertNotNull($address->getCreatedAt());
    }

    public function testAlreadyCreateAddress()
    {
        $cityRepositoryMock = $this->createMock(CityRepositoryInterface::class);
        $addressRepositoryMock = $this->createMock(AddressRepositoryInterface::class);

        $addressService = new CreateAddressService($addressRepositoryMock, $cityRepositoryMock);

        $country = CountryFixture::getOne('Russia', 'RU');
        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $city = CityFixture::getOne($region, 'city', 'Moskva');

        $cityRepositoryMock->method('ofTypeAndName')->willReturn($city);

        $addressDto = DaDataAddressDtoFixture::getOne();

        $addressRepositoryMock->method('ofAddress')
            ->willReturn(AddressFixture::getOneFromAddressDto($city, $addressDto));

        $this->expectException(AddressAlreadyCreatedException::class);
        $addressService->create($addressDto);
    }

    public function testCreateDeactivatedAddress()
    {
        $cityRepositoryMock = $this->createMock(CityRepositoryInterface::class);
        $addressRepositoryMock = $this->createMock(AddressRepositoryInterface::class);

        $addressService = new CreateAddressService($addressRepositoryMock, $cityRepositoryMock);

        $country = CountryFixture::getOne('Russia', 'RU');
        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $city = CityFixture::getOne($region, 'city', 'Moskva');

        $cityRepositoryMock->method('ofTypeAndName')->willReturn($city);

        $addressDto = DaDataAddressDtoFixture::getOne();

        $addressRepositoryMock->method('ofAddressDeactivated')
            ->willReturn(AddressFixture::getOneFromAddressDto($city, $addressDto, false));

        $this->expectException(AddressDeactivatedException::class);
        $addressService->create($addressDto);
    }

    public function testCreateDeletedAddress()
    {
        $cityRepositoryMock = $this->createMock(CityRepositoryInterface::class);
        $addressRepositoryMock = $this->createMock(AddressRepositoryInterface::class);

        $addressService = new CreateAddressService($addressRepositoryMock, $cityRepositoryMock);

        $country = CountryFixture::getOne('Russia', 'RU');
        $region = RegionFixture::getOne($country, 'Moskva', 'MOW');
        $city = CityFixture::getOne($region, 'city', 'Moskva');

        $cityRepositoryMock->method('ofTypeAndName')->willReturn($city);

        $addressDto = DaDataAddressDtoFixture::getOne();

        $addressRepositoryMock->method('ofAddressDeleted')
            ->willReturn(AddressFixture::getOneFromAddressDto($city, $addressDto, true, true));

        $this->expectException(AddressDeletedException::class);
        $addressService->create($addressDto);
    }
}
