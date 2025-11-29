<?php

namespace App\Infrastructure\DaData\Response\Denormalizer;

use App\Infrastructure\DaData\Exception\DaDataException;
use App\Infrastructure\DaData\Response\Dto\DaDataOktmoDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DaDataOktmoDenormalizer implements DenormalizerInterface
{
    /**
     * @param array $data
     * @throws DaDataException
     */
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): ?DaDataOktmoDto
    {
        if (empty($data)) {
            throw new DaDataException('Не удалось получить адрес из DaData');
        }

        return new DaDataOktmoDto(
            $data['oktmo'] ?? null,
            $data['area_type'] ?? null,
            $data['area_code'] ?? null,
            $data['area'] ?? null,
            $data['subarea_type'] ?? null,
            $data['subarea_code'] ?? null,
            $data['subarea'] ?? null
        );
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return $type === DaDataOktmoDto::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            DaDataOktmoDto::class => true,
        ];
    }
}
