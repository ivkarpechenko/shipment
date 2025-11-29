<?php

namespace App\Infrastructure\DaData\Response\Denormalizer;

use App\Domain\Address\Service\Dto\AddressDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DaDataSuggestAddressDtoDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): ?AddressDto
    {
        $address = reset($data);

        return new AddressDto(
            address: $address['unrestricted_value'],
            house: $address['data']['house'] ?? $address['data']['stead'] ?? '',
            country: $address['data']['country'],
            countryIsoCode: $address['data']['country_iso_code'],
            region: $address['data']['region'],
            regionIsoCode: $address['data']['region_iso_code'] ?? null,
            city: $address['data']['city'] ?? $address['data']['area'],
            cityType: $address['data']['city_type_full'] ?? $address['data']['area_type_full'],
            latitude: $address['data']['geo_lat'],
            longitude: $address['data']['geo_lon'],
            postalCode: $address['data']['postal_code'],
            street: $address['data']['street_with_type'],
            flat: $address['data']['settlement_with_type'] ?? null,
            entrance: $address['data']['entrance'] ?? null,
            floor: $address['data']['floor'] ?? null,
            settlement: $address['data']['flat'] ?? null,
            inputData: $address
        );
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return $type === AddressDto::class
            && !empty($data)
            && is_array($data)
            && array_key_exists('unrestricted_value', reset($data));
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            AddressDto::class => true,
        ];
    }
}
