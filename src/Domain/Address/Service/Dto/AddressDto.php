<?php

namespace App\Domain\Address\Service\Dto;

readonly class AddressDto
{
    public function __construct(
        public string $address,
        public string $house,
        public string $country,
        public string $countryIsoCode,
        public string $region,
        public ?string $regionIsoCode,
        public string $city,
        public string $cityType,
        public ?float $latitude,
        public ?float $longitude,
        public ?string $postalCode,
        public ?string $street,
        public ?string $flat,
        public ?string $entrance,
        public ?string $floor,
        public ?string $settlement,
        public ?array $inputData
    ) {
    }
}
