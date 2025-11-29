<?php

namespace App\Infrastructure\DeliveryService\Dellin\Service\Response\Denormalizer;

use App\Infrastructure\DeliveryService\Dellin\Service\Response\Dto\DellinSessionDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DellinSessionDtoDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): DellinSessionDto
    {
        return new DellinSessionDto(
            $data['data']['sessionID'],
            $data['data']['session']['expired'],
            \DateTime::createFromFormat('Y-m-d H:i:s', $data['data']['session']['expireTime']),
        );
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return $type === DellinSessionDto::class
                 && is_array($data)
                 && array_key_exists('data', $data);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            DellinSessionDto::class => true,
        ];
    }
}
