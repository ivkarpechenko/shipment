<?php

namespace App\Infrastructure\DeliveryService\Dellin\Service\Response\Denormalizer;

use App\Infrastructure\DeliveryService\Dellin\Service\Response\Dto\DellinAddressDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DellinAddressDtoDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): ?DellinAddressDto
    {
        $response = reset($data['cleanEssenceResponse']['data']);

        if (empty($response['result'])) {
            return null;
        }

        return DellinAddressDto::fromArray($response);
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return $type === DellinAddressDto::class && array_key_exists('cleanEssenceResponse', $data ?? []);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            DellinAddressDto::class => true,
        ];
    }
}
