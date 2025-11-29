<?php

namespace App\Tests\Fixture\Shipment;

use App\Application\Shipment\Command\Dto\BulkCreateShipmentDto;
use App\Application\Shipment\Command\Dto\ContactDto;

class BulkCreateShipmentDtoFixture
{
    public static function getOne(
        string $to,
        ContactDto $recipient,
        string $currencyCode,
        array $products = [],
        array $cargoRestrictions = [],
    ): BulkCreateShipmentDto {
        return new BulkCreateShipmentDto(
            $to,
            $recipient,
            $currencyCode,
            $products,
            $cargoRestrictions,
        );
    }

    public static function getOneFilled(
        ?string $to = null,
        ?ContactDto $recipient = null,
        ?string $currencyCode = null,
        array $products = [],
        array $cargoRestrictions = [],
    ): BulkCreateShipmentDto {
        return new BulkCreateShipmentDto(
            $to ?: 'to address',
            $recipient ?: ContactDtoFixture::getOne('test@gmail.com', 'recipient', [
                '+8888888888',
            ]),
            $currencyCode ?: 'RUB',
            $products ?: [
                ProductDtoFixture::getOneFilled(),
            ],
            $cargoRestrictions ?: [
                CargoRestrictionDtoFixture::getOneFilled(),
            ]
        );
    }
}
