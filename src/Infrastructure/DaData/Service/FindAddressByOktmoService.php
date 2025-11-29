<?php

namespace App\Infrastructure\DaData\Service;

use App\Infrastructure\DaData\Exception\DaDataException;
use App\Infrastructure\DaData\Response\Denormalizer\DaDataOktmoDenormalizer;
use App\Infrastructure\DaData\Response\Dto\DaDataOktmoDto;
use Dadata\DadataClient;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class FindAddressByOktmoService
{
    public function __construct(
        private DadataClient $dadataClient,
        private LoggerInterface $logger,
        private DaDataOktmoDenormalizer $denormalizer,
    ) {
    }

    /**
     * Найти адрес в DaData по передаваемому ОКТМО
     *
     * @throws DaDataException|GuzzleException
     */
    public function find(string $oktmo): ?DaDataOktmoDto
    {
        try {
            // RD-7760 DaData не принимает больше 8 символов
            if (strlen($oktmo) > 8) {
                $oktmo = substr($oktmo, 0, 8);
            }
            $response = $this->dadataClient->findById('oktmo', $oktmo);
            $data = $response[0]['data'] ?? null;

            return $this->denormalizer->denormalize($data, DaDataOktmoDto::class);
        } catch (DaDataException $e) {
            $this->logger->error(sprintf('Ошибка при работе с DaData: %s', $e->getMessage()));

            throw new DaDataException('Возникла ошибка при обращении в DaData');
        }
    }
}
