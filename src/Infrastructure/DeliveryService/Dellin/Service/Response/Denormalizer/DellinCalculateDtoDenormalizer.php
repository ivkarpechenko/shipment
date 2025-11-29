<?php

namespace App\Infrastructure\DeliveryService\Dellin\Service\Response\Denormalizer;

use App\Infrastructure\DeliveryService\Dellin\Service\Response\Dto\DellinCalculateDto;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DellinCalculateDtoDenormalizer implements DenormalizerInterface
{
    public function denormalize(
        mixed $data,
        string $type,
        ?string $format = null,
        array $context = []
    ): DellinCalculateDto {
        $period = !is_null($context['pickupPoint']) ? $this->getPickupMinPeriod($data['data']['orderDates'])
            : $this->getMinPeriod($data['data']['orderDates']);

        return new DellinCalculateDto(
            $data['data']['derival']['price'],
            $data['data']['arrival']['price'],
            $data['data']['deliveryTerm'],
            $data['data']['insurance'],
            $period,
            $period,
            $data['data']['availableDeliveryTypes'][$context['tariffPlanCode']], // стоимость доставки
            $data['data']['price'], // общая стоимость доставки с учетём всех затрат.
        );
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null): bool
    {
        return $type === DellinCalculateDto::class && array_key_exists('data', $data ?? []);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            DellinCalculateDto::class => true,
        ];
    }

    protected function getMinPeriod(array $orderDates): int
    {
        return (int) (new \DateTime('now'))
            ->diff(new \DateTime($orderDates['derivalFromOspReceiver']))
            ->format('%a');
    }

    protected function getPickupMinPeriod(array $orderDates): int
    {
        return (int) (new \DateTime('now'))
            ->diff(\DateTime::createFromFormat('Y-m-d H:i:s', $orderDates['giveoutFromOspReceiver']))
            ->format('%a');
    }
}
