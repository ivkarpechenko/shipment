<?php

declare(strict_types=1);

namespace App\Infrastructure\DeliveryService\Dellin\Service\Response\Denormalizer;

use App\Infrastructure\DeliveryService\Dellin\Service\Response\Dto\DellinPickupPointDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DellinPickupPointDtoDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): DellinPickupPointDto
    {
        $worktable = current(
            array_filter(
                $data['worktables']['worktable'],
                function (array $worktable) {
                    return $worktable['department'] === 'Приём и выдача груза' || $worktable['department'] === 'Прием и выдача груза';
                }
            )
        );

        return new DellinPickupPointDto(
            name: $data['name'],
            code: $data['id'],
            type: 'PVZ',
            weightMin: 0,
            weightMax: $data['maxWeight'],
            address: $data['fullAddress'],
            latitude: !is_null($data['latitude']) ? (float) $data['latitude'] : null,
            longitude: !is_null($data['longitude']) ? (float) $data['longitude'] : null,
            width: !is_null($data['maxWidth']) ? (float) $data['maxWidth'] * 100 : null,
            height: !is_null($data['maxHeight']) ? (float) $data['maxHeight'] * 100 : null,
            depth: !is_null($data['maxLength']) ? (float) $data['maxLength'] * 100 : null,
            phones: array_map(function (array $item) {
                return sprintf('+%s', preg_replace('~\D+~', '', $item['number']));
            }, $data['phones']),
            workTime: $worktable['timetable']
        );
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return $type === DellinPickupPointDto::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            DellinPickupPointDto::class => true,
        ];
    }
}
