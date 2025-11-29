<?php

namespace App\Infrastructure\DeliveryService\CDEK\Service;

use App\Infrastructure\DeliveryService\CDEK\Service\Exception\CdekPickupPointErrorException;
use App\Infrastructure\DeliveryService\CDEK\Service\Response\Dto\CdekAccessTokenDto;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CdekPickupPointHttpClientService
{
    protected const AUTH_CACHE_KEY = 'cdek_auth_cache_key';

    public function __construct(
        public HttpClientInterface $pvzcdekClient,
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
            $response = $this->pvzcdekClient->request($method, $url, $options);

            return $response->toArray();
        } catch (\Throwable $exception) {
            throw new CdekPickupPointErrorException($exception->getMessage(), $exception->getCode());
        }
    }

    final protected function refreshAuth(): CdekAccessTokenDto
    {
        return $this->cache->get(self::AUTH_CACHE_KEY, function (ItemInterface $item) {
            $response = $this->pvzcdekClient->request('POST', 'oauth/token', [
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
