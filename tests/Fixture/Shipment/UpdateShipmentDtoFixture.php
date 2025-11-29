<?php

namespace App\Tests\Fixture\Shipment;

use App\Application\Shipment\Command\Dto\ContactDto;
use App\Application\Shipment\Command\Dto\PackageDto;
use App\Application\Shipment\Command\Dto\UpdateShipmentDto;
use App\Domain\PickupPoint\Entity\PickupPoint;

class UpdateShipmentDtoFixture
{
    public static function getOne(
        ?string $from = null,
        ?string $to = null,
        ?ContactDto $sender = null,
        ?ContactDto $recipient = null,
        ?string $currencyCode = null,
        // @var PackageDto[] $packages
        array $packages = [],
        ?\DateTime $psd = null,
        ?\DateTime $psdStartTime = null,
        ?\DateTime $psdEndTime = null,
        ?PickupPoint $pickupPoint = null,
    ): UpdateShipmentDto {
        return new UpdateShipmentDto(
            $from,
            $to,
            $sender,
            $recipient,
            $currencyCode,
            $packages,
            $psd,
            $psdStartTime,
            $psdEndTime,
            $pickupPoint,
        );
    }

    public static function getOneFilled(
        ?string $from = null,
        ?string $to = null,
        ?ContactDto $sender = null,
        ?ContactDto $recipient = null,
        ?string $currencyCode = null,
        // @var PackageDto[] $packages
        array $packages = [],
        ?\DateTime $psd = null,
        ?\DateTime $psdStartTime = null,
        ?\DateTime $psdEndTime = null,
        ?PickupPoint $pickupPoint = null,
    ): UpdateShipmentDto {
        return new UpdateShipmentDto(
            $from ?: 'from address',
            $to ?: 'to address',
            $sender ?: ContactDtoFixture::getOne('test@gmail.com', 'sender'),
            $recipient ?: ContactDtoFixture::getOne('test@gmail.com', 'recipient'),
            $currencyCode ?: 'RUB',
            $packages ?: [
                PackageDtoFixture::getOne(1, 1, 1, 1, 1),
            ],
            $psd ?: new \DateTime('now'),
            $psdStartTime ?: new \DateTime('now'),
            $psdEndTime ?: new \DateTime('now'),
            $pickupPoint
        );
    }
}
