<?php

namespace App\Application\Shipment\Command\Dto;

readonly class BulkCreateShipmentTekTorgDto
{
    public function __construct(
        public ?string $okato,
        public ?string $oktmo,
        public ContactDto $recipient,
        public string $currencyCode,

        /**
         * @var ProductDto[]
         */
        public array $products,

        /**
         * @var CargoRestrictionDto[]
         */
        public array $cargoRestrictions
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['okato'] ?? null,
            $data['oktmo'] ?? null,
            ContactDto::fromArray($data['recipient'] ?? []),
            $data['currencyCode'],
            array_map(function ($product) {
                return ProductDto::fromArray($product);
            }, $data['products'] ?? []),
            array_map(function ($cargoRestriction) {
                return CargoRestrictionDto::fromArray($cargoRestriction);
            }, $data['cargoRestrictions'] ?? []),
        );
    }
}
