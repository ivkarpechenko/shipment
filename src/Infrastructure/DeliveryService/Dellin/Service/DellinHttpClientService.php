<?php

namespace App\Infrastructure\DeliveryService\Dellin\Service;

use App\Infrastructure\DeliveryService\Dellin\Exception\DellinCalculateErrorException;
use App\Infrastructure\DeliveryService\Dellin\Service\Response\Dto\DellinSessionDto;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DellinHttpClientService
{
    protected const AUTH_CACHE_KEY = 'dellin_auth_cache_key';

    public function __construct(
        public HttpClientInterface $dellinClient,
        public ParameterBagInterface $parameter,
        public SerializerInterface $serializer,
        public CacheInterface $cache
    ) {
    }

    public function request(string $method, string $url, array $options = []): array
    {
        $response = null;

        try {
            $dellinSessionDto = $this->refreshSession();

            $options = [
                'json' => array_merge_recursive(
                    [
                        'appkey' => $this->parameter->get('dellin_token'),
                        'sessionID' => $dellinSessionDto->sessionId,
                    ],
                    $options
                ),
            ];

            $response = $this->dellinClient->request($method, $url, $options);

            return $response->toArray();
        } catch (\Throwable $exception) {
            throw new DellinCalculateErrorException($response?->getContent(false), $exception->getCode());
        }
    }

    final protected function refreshSession(): DellinSessionDto
    {
        return $this->cache->get(self::AUTH_CACHE_KEY, function (ItemInterface $item) {
            $sessionResponseArray = $this->dellinClient->request('POST', 'v3/auth/login.json', [
                'json' => [
                    'appkey' => $this->parameter->get('dellin_token'),
                    'login' => $this->parameter->get('dellin_account'),
                    'password' => $this->parameter->get('dellin_secure_password'),
                ],
            ])->toArray();

            if (!array_key_exists('data', $sessionResponseArray)) {
                throw new \Exception('Session generation failed, the service dellin did not respond to the request, please try again.');
            }

            $sessionInfoResponseInfo = $this->dellinClient->request('POST', 'v3/auth/session_info.json', [
                'json' => [
                    'appkey' => $this->parameter->get('dellin_token'),
                    'sessionID' => $sessionResponseArray['data']['sessionID'],
                ],
            ])->toArray();

            $dellinSessionDto = $this->serializer->denormalize(
                array_merge_recursive($sessionResponseArray, $sessionInfoResponseInfo),
                DellinSessionDto::class
            );

            $item->expiresAfter(
                ($dellinSessionDto->expireTime->format('U') - (new \DateTime('now'))->format('U')) - 100
            );

            return $dellinSessionDto;
        });
    }
}
