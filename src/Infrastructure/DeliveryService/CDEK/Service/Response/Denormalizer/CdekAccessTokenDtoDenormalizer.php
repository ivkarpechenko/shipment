<?php

namespace App\Infrastructure\DeliveryService\CDEK\Service\Response\Denormalizer;

use App\Infrastructure\DeliveryService\CDEK\Service\Response\Dto\CdekAccessTokenDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CdekAccessTokenDtoDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): CdekAccessTokenDto
    {
        return new CdekAccessTokenDto(
            $data['access_token'],
            $data['token_type'],
            $data['expires_in'],
            $data['scope'],
            $data['jti']
        );
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return $type === CdekAccessTokenDto::class
            && is_array($data)
            && array_key_exists('access_token', $data);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            CdekAccessTokenDto::class => true,
        ];
    }
}
