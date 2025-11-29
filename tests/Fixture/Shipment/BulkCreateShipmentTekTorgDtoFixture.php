<?php

namespace App\Tests\Fixture\Shipment;

use App\Application\Shipment\Command\Dto\BulkCreateShipmentTekTorgDto;
use App\Application\Shipment\Command\Dto\ContactDto;

class BulkCreateShipmentTekTorgDtoFixture
{
    public static function getOne(
        string $okato,
        ContactDto $recipient,
        string $currencyCode,
        array $products = [],
        array $cargoRestrictions = [],
        ?string $oktmo = null
    ): BulkCreateShipmentTekTorgDto {
        return new BulkCreateShipmentTekTorgDto(
            okato: $okato,
            oktmo: $oktmo,
            recipient: $recipient,
            currencyCode: $currencyCode,
            products: $products,
            cargoRestrictions: $cargoRestrictions,
        );
    }

    public static function getOneFilled(
        ?string $okato = null,
        ?string $oktmo = null,
        ?ContactDto $recipient = null,
        ?string $currencyCode = null,
        array $products = [],
        array $cargoRestrictions = []
    ): BulkCreateShipmentTekTorgDto {
        return new BulkCreateShipmentTekTorgDto(
            $okato,
            $oktmo,
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
