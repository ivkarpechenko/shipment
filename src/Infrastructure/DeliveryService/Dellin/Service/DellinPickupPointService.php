<?php

declare(strict_types=1);

namespace App\Infrastructure\DeliveryService\Dellin\Service;

use App\Infrastructure\DeliveryService\Dellin\Exception\DellinCalculateErrorException;
use App\Infrastructure\DeliveryService\Dellin\Service\Response\Dto\DellinPickupPointDto;
use App\Infrastructure\DeliveryService\Dellin\Service\Response\Dto\DellinTerminalDto;
use Symfony\Component\Serializer\SerializerInterface;

readonly class DellinPickupPointService
{
    public function __construct(
        private DellinHttpClientService $client,
        private SerializerInterface $serializer,
    ) {
    }

    /**
     * @throws DellinCalculateErrorException
     */
    public function deliveryPoints(): array
    {
        $response = $this->client->request(
            'POST',
            'v3/public/terminals.json'
        );

        $terminalDto = $this->client->serializer->denormalize($response, DellinTerminalDto::class);
        $terminals = $this->client->request('GET', $terminalDto->url);

        $pickupPoints = [];
        foreach ($terminals['city'] as $city) {
            foreach ($city['terminals']['terminal'] as $terminal) {
                $worktables = array_filter(
                    $terminal['worktables']['worktable'],
                    function (array $worktable) {
                        return $worktable['department'] === 'Приём и выдача груза' || $worktable['department'] === 'Прием и выдача груза';
                    }
                );
                if (empty($worktables)) {
                    continue;
                }

                if (!$terminal['isOffice'] && $terminal['receiveCargo'] && $terminal['giveoutCargo']) {
                    $pickupPoints[] = $this->serializer->denormalize($terminal, DellinPickupPointDto::class);
                }
            }
        }

        return $pickupPoints;
    }
}
