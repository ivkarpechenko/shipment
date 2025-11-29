<?php

namespace App\Infrastructure\DeliveryService\CDEK\Service\Response\Denormalizer;

use App\Infrastructure\DeliveryService\CDEK\Service\Response\Dto\CdekPickupPointDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CdekPickupPointDtoDenormalizer implements DenormalizerInterface
{
    public function __construct(public CdekErrorDtoDenormalizer $errorsDtoDenormalizer)
    {
    }

    public function denormalize(mixed $data, ?string $type = null, ?string $format = null, array $context = []): CdekPickupPointDto
    {
        $phones = [];
        if (array_key_exists('phones', $data)) {
            foreach ($data['phones'] as $phone) {
                $phones[] = $phone['number'];
            }
        }

        return new CdekPickupPointDto(
            workTime: $data['work_time'],
            code: $data['code'],
            type: $data['type'],
            weightMin: $data['weight_min'] ?? null,
            weightMax: $data['weight_max'] ?? null,
            address: $data['location']['address_full'],
            latitude: $data['location']['latitude'],
            longitude: $data['location']['longitude'],
            width: $data['dimensions'][0]['width'] ?? null,
            height: $data['dimensions'][0]['height'] ?? null,
            depth: $data['dimensions'][0]['depth'] ?? null,
            phones: $phones
        );
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return $type === CdekPickupPointDto::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            CdekPickupPointDto::class => true,
        ];
    }
}
