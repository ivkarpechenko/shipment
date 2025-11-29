<?php

namespace App\Infrastructure\DaData\Service;

use App\Application\Address\Query\External\FindExternalAddressInterface;
use App\Domain\Address\Service\Dto\AddressDto;
use Dadata\DadataClient;
use Symfony\Component\Serializer\SerializerInterface;

class FindByAddressService implements FindExternalAddressInterface
{
    public function __construct(public DadataClient $dadataClient, public SerializerInterface $serializer)
    {
    }

    public function find(string $address): ?AddressDto
    {
        $response = $this->dadataClient->suggest('address', $address, 1);

        if (empty($response)) {
            $response = $this->dadataClient->clean('address', $address);

            if (empty($response)) {
                return null;
            }
        }

        return $this->serializer->denormalize($response, AddressDto::class);
    }
}
