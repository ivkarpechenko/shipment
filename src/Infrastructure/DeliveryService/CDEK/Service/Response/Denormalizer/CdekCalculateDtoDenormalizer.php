<?php

namespace App\Infrastructure\DeliveryService\CDEK\Service\Response\Denormalizer;

use App\Infrastructure\DeliveryService\CDEK\Service\Response\Dto\CdekCalculateDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CdekCalculateDtoDenormalizer implements DenormalizerInterface
{
    public function __construct(public CdekErrorDtoDenormalizer $errorsDtoDenormalizer)
    {
    }

    public function denormalize(mixed $data, ?string $type = null, ?string $format = null, array $context = []): CdekCalculateDto
    {
        return new CdekCalculateDto(
            $data['period_min'],
            $data['currency'],
            $data['delivery_sum'],
            $data['weight_calc'],
            $data['period_max'],
            $data['total_sum']
        );
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return $type === CdekCalculateDto::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            CdekCalculateDto::class => true,
        ];
    }
}
