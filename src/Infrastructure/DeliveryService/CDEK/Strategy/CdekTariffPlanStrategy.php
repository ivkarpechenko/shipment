<?php

namespace App\Infrastructure\DeliveryService\CDEK\Strategy;

use App\Domain\DeliveryService\Enum\DeliveryServiceEnum;
use App\Domain\TariffPlan\Strategy\TariffPlanStrategyInterface;

class CdekTariffPlanStrategy implements TariffPlanStrategyInterface
{
    // Тарифы для обычной доставки | https://api-docs.cdek.ru/63345430.html
    private const ALLOWED_REGULAR_TARIFF_CODE_LIST = [
        3, 57, 58, 59, 60, 61, 777, 786, 795, 804, 778, 787, 796, 805,
        779, 788, 797, 806, 62, 121, 122, 123, 63, 124, 125, 126, 480,
        481, 482, 483, 485, 486, 751, 66, 67, 68, 69, 676, 677, 678, 679,
        686, 687, 688, 689, 696, 697, 698, 699, 706, 707, 708, 709, 716,
        717, 718, 719, 533, 534, 535, 536,
    ];

    // Тарифы для ИМ | https://api-docs.cdek.ru/63345430.html
    private const ALLOWED_IM_TARIFF_CODE_LIST = [
        7, 8, 136, 137, 138, 139, 231, 232, 233, 234, 291, 293, 294,
        295, 509, 510, 366, 368, 378, 184, 185, 186, 187, 497, 498,
    ];

    public function execute(string $deliveryServiceCode, string $tariffPlanCode): bool
    {
        return in_array($tariffPlanCode, array_merge(
            self::ALLOWED_REGULAR_TARIFF_CODE_LIST,
            self::ALLOWED_IM_TARIFF_CODE_LIST
        ));
    }

    public function supports(string $deliveryServiceCode): bool
    {
        return DeliveryServiceEnum::CDEK === DeliveryServiceEnum::from($deliveryServiceCode);
    }
}
