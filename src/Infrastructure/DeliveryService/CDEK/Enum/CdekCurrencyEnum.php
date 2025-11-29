<?php

namespace App\Infrastructure\DeliveryService\CDEK\Enum;

enum CdekCurrencyEnum: int
{
    case RUB = 1;
    case KZT = 2;
    case USD = 3;
    case EUR = 4;
    case GBP = 5;
    case CNY = 6;
    case BYN = 7;
    case UAH = 8;
    case KGS = 9;
    case AMD = 10;
    case TRY = 11;
    case THB = 12;
    case KRW = 13;
    case AED = 14;
    case UZS = 15;
    case MNT = 16;
    case PLN = 17;
    case AZN = 18;
    case GEL = 19;
    case JPY = 20;

    public static function fromName(string $name): string
    {
        foreach (self::cases() as $status) {
            if ($name === $status->name) {
                return $status->value;
            }
        }

        throw new \ValueError("{$name} is not a valid backing value for enum " . self::class);
    }
}
