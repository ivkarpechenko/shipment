<?php

namespace App\Infrastructure\DeliveryService\Dellin\Service\Request\Normalizer;

use App\Infrastructure\DeliveryService\Dellin\Service\Request\Dto\DellinFindAddressDto;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DellinFindAddressDtoNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        return [
            'data' => $object->data,
            'type' => $object->type,
            'mode' => $object->mode,
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null): bool
    {
        return $data instanceof DellinFindAddressDto;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            DellinFindAddressDto::class => true,
        ];
    }
}
