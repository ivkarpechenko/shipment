<?php

namespace App\Infrastructure\DeliveryService\Dellin\Service\Request\Normalizer;

use App\Infrastructure\DeliveryService\Dellin\Service\Request\Dto\DellinPackageDto;
use App\Infrastructure\DeliveryService\Dellin\Service\Request\Dto\DellinShipmentDto;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DellinShipmentDtoNormalizer implements NormalizerInterface
{
    protected const WORKING_START_TIME = '09:00';

    protected const WORKING_END_TIME = '18:00';

    protected const UID = '0xbfcaad5766424ecd4eb5b4ede1e6bc97'; // Оборудование (строительное)

    protected const PAYMENT_TYPE = 'noncash';

    protected const PAYMENT_CITY = '7700000000000000000000000'; // Москва

    protected const REQUESTER_ROLE = 'third';

    /**
     * https://wiki.diam-almaz.ru/books/logistika/page/3-integratsiya-s-lo-delovye-linii-i-sdek-po-kalkulyatoru-v11
     * @param DellinShipmentDto $object
     */
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        $arrival = [
            'variant' => 'address',
            'address' => [
                'search' => $object->to->getInputDataBy('value')->current(),
            ],
            'time' => [
                'worktimeStart' => self::WORKING_START_TIME,
                'worktimeEnd' => self::WORKING_END_TIME,
            ],
        ];
        if (!is_null($object->pickupPoint)) {
            $arrival = [
                'variant' => 'terminal',
                'terminalID' => $object->pickupPoint->getCode(),
                'time' => [
                    'worktimeStart' => self::WORKING_START_TIME,
                    'worktimeEnd' => self::WORKING_END_TIME,
                ],
            ];
        }

        return [
            'delivery' => [
                'deliveryType' => [
                    'type' => $object->tariffPlan->getCode(),
                ],
                'derival' => [
                    'produceDate' => $object->psd->format('Y-m-d'), // psd
                    'variant' => 'address',
                    'address' => [
                        'search' => $object->from->getInputDataBy('value')->current(),
                    ],
                    'time' => [
                        'worktimeStart' => $object->psdStartTime->format('G:i'),
                        'worktimeEnd' => $object->psdEndTime->format('G:i'),
                    ],
                ],
                'arrival' => $arrival,
            ],
            'cargo' => [
                'quantity' => $object->packages->count(), // Передаем кол-во упаковок
                'length' => max(
                    $object->packages->map(function (DellinPackageDto $package) {
                        return $package->length;
                    })->getValues()
                ), // Передаем макс. длину среди упаковок отправления
                'width' => max(
                    $object->packages->map(function (DellinPackageDto $package) {
                        return $package->width;
                    })->getValues()
                ), // Передаем макс. ширину среди упаковок отправления
                'height' => max(
                    $object->packages->map(function (DellinPackageDto $package) {
                        return $package->height;
                    })->getValues()
                ), // Передаем макс. высоту среди упаковок отправления
                'weight' => max(
                    $object->packages->map(function (DellinPackageDto $package) {
                        return $package->weight;
                    })->getValues()
                ), // Передаем макс. вес среди упаковок отправления
                'totalVolume' => array_sum(
                    $object->packages->map(function (DellinPackageDto $package) {
                        return $package->length * $package->width * $package->height;
                    })->getValues()
                ),
                'totalWeight' => array_sum(
                    $object->packages->map(function (DellinPackageDto $package) {
                        return $package->weight;
                    })->getValues()
                ),
                'oversizedWeight' => array_sum(
                    $object->packages->map(function (DellinPackageDto $package) {
                        return $package->weight;
                    })->getValues()
                ), // Передаем сумму весов упаковок отправления
                'oversizedVolume' => array_sum(
                    $object->packages->map(function (DellinPackageDto $package) {
                        return $package->length * $package->width * $package->height;
                    })->getValues()
                ), // Передаем сумму объемов товаров отправления
                'freightUID' => self::UID,
            ],
            'hazardClass' => 0,
            'insurance' => [
                'statedValue' => array_sum(
                    $object->packages->map(function (DellinPackageDto $package) {
                        return $package->price;
                    })->getValues()
                ), // общая стоимость груза,
                'term' => true,
            ],
            'members' => [
                'requester' => [
                    'role' => self::REQUESTER_ROLE,
                ],
            ],
            'payment' => [
                'type' => self::PAYMENT_TYPE,
                'paymentCity' => self::PAYMENT_CITY,
            ],
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null): bool
    {
        return $data instanceof DellinShipmentDto;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            DellinShipmentDto::class => true,
        ];
    }
}
