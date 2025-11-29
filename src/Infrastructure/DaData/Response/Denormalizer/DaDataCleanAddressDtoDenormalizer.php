<?php

namespace App\Infrastructure\DaData\Response\Denormalizer;

use App\Domain\Address\Service\Dto\AddressDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DaDataCleanAddressDtoDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): ?AddressDto
    {
        return new AddressDto(
            $data['result'],
            $data['house'],
            $data['country'],
            $data['country_iso_code'],
            $data['region'],
            $data['region_iso_code'],
            $data['city'],
            $data['city_type_full'],
            $data['geo_lat'],
            $data['geo_lon'],
            $data['postal_code'],
            $data['street_with_type'],
            $data['settlement_with_type'],
            $data['entrance'],
            $data['floor'],
            $data['flat'],
            [
                'value' => $data['source'],
                'unrestricted_value' => $data['result'],
                'data' => $data,
            ]
        );
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return $type === AddressDto::class
            && !empty($data)
            && is_array($data)
            && array_key_exists('result', $data);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            AddressDto::class => true,
        ];
    }
}
