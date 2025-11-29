<?php

namespace App\Infrastructure\DeliveryService\CDEK\Service;

use App\Infrastructure\DeliveryService\CDEK\Service\Exception\CdekCalculateErrorException;
use App\Infrastructure\DeliveryService\CDEK\Service\Response\Dto\CdekAccessTokenDto;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CdekHttpClientService
{
    protected const AUTH_CACHE_KEY = 'cdek_auth_cache_key';

    public function __construct(
        public HttpClientInterface $cdekClient,
        public ParameterBagInterface $parameter,
        public SerializerInterface $serializer,
        public CacheInterface $cache
    ) {
    }

    public function request(string $method, string $url, array $options = []): array
    {
        $response = null;

        try {
            $cdekAccessTokenDto = $this->refreshAuth();

            $options = [
                'headers' => [
                    'Authorization' => 'Bearer ' . $cdekAccessTokenDto->accessToken,
                ],
                'body' => json_encode($options),
            ];

            $response = $this->cdekClient->request($method, $url, $options);

            return $response->toArray();
        } catch (\Throwable $exception) {
            throw new CdekCalculateErrorException($response?->getContent(false), $exception->getCode());
        }
    }

    final protected function refreshAuth(): CdekAccessTokenDto
    {
        return $this->cache->get(self::AUTH_CACHE_KEY, function (ItemInterface $item) {
            $response = $this->cdekClient->request('POST', 'oauth/token', [
                'headers' => [
                    'Content-Type' => 'multipart/form-data',
                ],
                'body' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => $this->parameter->get('cdek_account'),
                    'client_secret' => $this->parameter->get('cdek_secure_password'),
                ],
            ]);

            $cdekAccessTokenDto = $this->serializer->denormalize($response->toArray(), CdekAccessTokenDto::class);

            $item->expiresAfter($cdekAccessTokenDto->expiresIn - 1);

            return $cdekAccessTokenDto;
        });
    }
}
