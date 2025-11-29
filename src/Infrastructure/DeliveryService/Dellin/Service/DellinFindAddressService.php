<?php

namespace App\Infrastructure\DeliveryService\Dellin\Service;

use App\Domain\Address\Entity\Address;
use App\Infrastructure\DeliveryService\Dellin\Service\Request\Dto\DellinFindAddressDto;
use App\Infrastructure\DeliveryService\Dellin\Service\Response\Dto\DellinAddressDto;
use Symfony\Component\Serializer\SerializerInterface;

class DellinFindAddressService
{
    public function __construct(
        public DellinHttpClientService $dellinHttpClientService,
        public SerializerInterface $serializer
    ) {
    }

    public function findByAddress(Address $address): ?DellinAddressDto
    {
        $findAddressResponse = $this->dellinHttpClientService->request(
            'POST',
            'v1/standardized_address.json',
            $this->serializer->normalize(DellinFindAddressDto::from(
                [
                    $address->getAddress(),
                ]
            ))
        );

        return $this->serializer->denormalize($findAddressResponse, DellinAddressDto::class);
    }
}
