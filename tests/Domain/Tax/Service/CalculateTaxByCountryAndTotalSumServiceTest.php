<?php

namespace App\Tests\Domain\Tax\Service;

use App\Domain\Country\Repository\CountryRepositoryInterface;
use App\Domain\Tax\Service\CalculateTaxByCountryAndTotalSumService;
use App\Domain\Tax\Service\CreateTaxService;
use App\Tests\DoctrineTestCase;
use App\Tests\Fixture\Country\CountryFixture;

class CalculateTaxByCountryAndTotalSumServiceTest extends DoctrineTestCase
{
    public function testCreateTax()
    {
        $countryRepository = $this->getContainer()->get(CountryRepositoryInterface::class);
        $service = $this->getContainer()->get(CreateTaxService::class);

        $countryRepository->create(CountryFixture::getOne('Russia', 'RU'));
        $country = $countryRepository->ofCode('RU');

        $service->create($country->getCode(), 'НДС', 0.2, 'price/(1+value)*value');

        $service = $this->getContainer()->get(CalculateTaxByCountryAndTotalSumService::class);
        $totalVat = $service->calculate($country, 12321);

        $this->assertEquals(2053.5, $totalVat);
    }
}
