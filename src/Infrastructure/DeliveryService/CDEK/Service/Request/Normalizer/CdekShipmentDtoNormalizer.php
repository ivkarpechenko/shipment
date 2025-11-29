<?php

namespace App\Infrastructure\DeliveryService\CDEK\Service\Request\Normalizer;

use App\Domain\DeliveryMethod\Enum\DeliveryMethodEnum;
use App\Domain\DeliveryService\Enum\DeliveryServiceEnum;
use App\Infrastructure\DeliveryService\CDEK\Enum\CdekCurrencyEnum;
use App\Infrastructure\DeliveryService\CDEK\Service\Request\Dto\CdekPackageDto;
use App\Infrastructure\DeliveryService\CDEK\Service\Request\Dto\CdekShipmentDto;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CdekShipmentDtoNormalizer implements NormalizerInterface
{
    public function __construct(
        public LoggerInterface $logger
    ) {
    }

    // https://wiki.diam-almaz.ru/books/logistika/page/3-integratsiya-s-lo-delovye-linii-i-sdek-po-kalkulyatoru-v11
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        /** @var CdekShipmentDto $object */
        $packages = [];
        foreach ($object->packages as $package) {
            $packages[] = [
                'weight' => $package->weight,
                'length' => $package->length,
                'width' => $package->width,
                'height' => $package->height,
            ];
        }
        if (!is_null($object->pickupPoint)
            && DeliveryMethodEnum::from($object->tariffPlan->getDeliveryMethod()->getCode()) === DeliveryMethodEnum::PVZ
            && DeliveryServiceEnum::from($object->tariffPlan->getDeliveryService()->getCode()) === DeliveryServiceEnum::CDEK
        ) {
            $toLocation = [
                'address' => $object->pickupPoint->getAddress(),
            ];
        } else {
            $toLocation = [
                'postal_code' => $object->to->getPostalCode(),
                'country_code' => $object->to->getCity()->getRegion()->getCountry()->getCode(),
                'city' => $object->to->getCity()->getName(),
                'address' => $object->to->getAddress(),
            ];
        }

        $data = [
            'date' => $object->psd->format('Y-m-d\TH:i:sO'), // ISO 8601,
            'type' => '2',
            'currency' => CdekCurrencyEnum::fromName($object->currency->getCode()),
            'tariff_code' => $object->tariffPlan->getCode(),
            'from_location' => [
                'postal_code' => $object->from->getPostalCode(),
                'country_code' => $object->from->getCity()->getRegion()->getCountry()->getCode(),
                'city' => $object->from->getCity()->getName(),
                'address' => $object->from->getAddress(),
            ],
            'to_location' => $toLocation,
            'services' => [
                [
                    'code' => 'INSURANCE',
                    'parameter' => array_sum(
                        $object->packages->map(function (CdekPackageDto $package) {
                            return $package->price;
                        })->getValues()
                    ),
                ],
            ],
            'packages' => $packages,
        ];
        $this->logger->info(json_encode($data, JSON_UNESCAPED_UNICODE));

        return $data;
    }

    public function supportsNormalization(mixed $data, ?string $format = null): bool
    {
        return $data instanceof CdekShipmentDto;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            CdekShipmentDto::class => true,
        ];
    }
}
