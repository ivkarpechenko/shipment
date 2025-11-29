<?php

namespace App\Application\Shipment\Command\Dto;

readonly class CreateShipmentDto
{
    public function __construct(
        public string $from,
        public string $to,
        public ContactDto $sender,
        public ContactDto $recipient,
        public string $currencyCode,
        /** @var PackageDto[] $packages */
        public array $packages,
        public \DateTime $psd,
        public \DateTime $psdStartTime,
        public \DateTime $psdEndTime
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['from'] ?? null,
            $data['to'] ?? null,
            ContactDto::fromArray($data['sender'] ?? []),
            ContactDto::fromArray($data['recipient'] ?? []),
            $data['currencyCode'],
            array_map(function ($package) {
                return PackageDto::fromArray($package);
            }, $data['packages'] ?? []),
            $data['psd']
                ? \DateTime::createFromFormat('Y-m-d', $data['psd'])
                : null,
            $data['psdStartTime']
                ? \DateTime::createFromFormat('H:i:s', $data['psdStartTime'])
                : null,
            $data['psdEndTime']
                ? \DateTime::createFromFormat('H:i:s', $data['psdEndTime'])
                : null,
        );
    }
}
