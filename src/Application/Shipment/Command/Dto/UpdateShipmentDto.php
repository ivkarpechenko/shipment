<?php

namespace App\Application\Shipment\Command\Dto;

use Symfony\Component\Uid\Uuid;

readonly class UpdateShipmentDto
{
    public function __construct(
        public ?string $from,
        public ?string $to,
        public ?ContactDto $sender,
        public ?ContactDto $recipient,
        public ?string $currencyCode,
        /** @var PackageDto[] $packages */
        public array $packages,
        public ?\DateTime $psd,
        public ?\DateTime $psdStartTime,
        public ?\DateTime $psdEndTime,
        public ?Uuid $pickupPointId,
    ) {
    }

    public static function fromArray(array $data): UpdateShipmentDto
    {
        return new self(
            from: $data['from'] ?? null,
            to: $data['to'] ?? null,
            sender: array_key_exists('sender', $data) && is_array($data['sender'])
                ? ContactDto::fromArray($data['sender'])
                : null,
            recipient: array_key_exists('recipient', $data) && is_array($data['recipient'])
                ? ContactDto::fromArray($data['recipient'])
                : null,
            currencyCode: $data['currencyCode'] ?? null,
            packages: array_map(function ($package) {
                return PackageDto::fromArray($package);
            }, $data['packages'] ?? []),
            psd: array_key_exists('psd', $data) && $data['psd']
                ? \DateTime::createFromFormat('Y-m-d', $data['psd'])
                : null,
            psdStartTime: array_key_exists('psdStartTime', $data) && $data['psdStartTime']
                ? \DateTime::createFromFormat('H:i:s', $data['psdStartTime'])
                : null,
            psdEndTime: array_key_exists('psdEndTime', $data) && $data['psdEndTime']
                ? \DateTime::createFromFormat('H:i:s', $data['psdEndTime'])
                : null,
            pickupPointId: array_key_exists('pickupPointId', $data) && $data['pickupPointId']
                ? Uuid::fromString($data['pickupPointId'])
                : null
        );
    }
}
