<?php

namespace App\Infrastructure\DeliveryService\CDEK\Service\Response\Denormalizer;

use App\Infrastructure\DeliveryService\CDEK\Service\Response\Dto\CdekErrorDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CdekErrorDtoDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): iterable
    {
        yield new CdekErrorDto(
            $data['code'],
            $data['message'],
        );
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return $type === CdekErrorDto::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            CdekErrorDto::class => true,
        ];
    }
}
