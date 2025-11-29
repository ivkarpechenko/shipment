<?php

declare(strict_types=1);

namespace App\Infrastructure\DeliveryService\Dellin\Service\Response\Denormalizer;

use App\Infrastructure\DeliveryService\Dellin\Service\Response\Dto\DellinTerminalDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DellinTerminalDtoDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): DellinTerminalDto
    {
        return new DellinTerminalDto(
            hash: $data['hash'],
            url: $data['url'],
        );
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return $type === DellinTerminalDto::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            DellinTerminalDto::class => true,
        ];
    }
}
