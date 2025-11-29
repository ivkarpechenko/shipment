<?php

namespace App\Tests\Fixture\Shipment;

use App\Application\Shipment\Command\Dto\ContactDto;
use App\Application\Shipment\Command\Dto\CreateShipmentDto;

class CreateShipmentDtoFixture
{
    public static function getOne(
        string $from,
        string $to,
        ContactDto $sender,
        ContactDto $recipient,
        string $currencyCode,
        array $packages = [],
        ?\DateTime $psd = null,
        ?\DateTime $psdStartTime = null,
        ?\DateTime $psdEndTime = null,
    ): CreateShipmentDto {
        return new CreateShipmentDto(
            $from,
            $to,
            $sender,
            $recipient,
            $currencyCode,
            $packages,
            $psd,
            $psdStartTime,
            $psdEndTime
        );
    }

    public static function getOneFilled(
        ?string $from = null,
        ?string $to = null,
        ?ContactDto $sender = null,
        ?ContactDto $recipient = null,
        ?string $currencyCode = null,
        array $packages = [],
        ?\DateTime $psd = null,
        ?\DateTime $psdStartTime = null,
        ?\DateTime $psdEndTime = null,
    ): CreateShipmentDto {
        return new CreateShipmentDto(
            $from ?: 'from address',
            $to ?: 'to address',
            $sender ?: ContactDtoFixture::getOne('test@gmail.com', 'sender', [
                '+7777777777',
            ]),
            $recipient ?: ContactDtoFixture::getOne('test@gmail.com', 'recipient', [
                '+8888888888',
            ]),
            $currencyCode ?: 'RUB',
            $packages ?: [
                PackageDtoFixture::getOne(1, 1, 1, 1, 1),
            ],
            $psd ?: (new \DateTime('now')),
            $psdStartTime ?: (new \DateTime('now')),
            $psdEndTime ?: (new \DateTime('now')),
        );
    }
}
