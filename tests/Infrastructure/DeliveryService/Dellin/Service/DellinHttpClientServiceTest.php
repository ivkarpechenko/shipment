<?php

namespace App\Tests\Infrastructure\DeliveryService\Dellin\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DellinHttpClientServiceTest extends KernelTestCase
{
    public function testGenerateSessionId()
    {
        $dellinHttpClient = $this->getContainer()->get('DellinHttpClientService');

        $this->assertInstanceOf(HttpClientInterface::class, $dellinHttpClient->dellinClient);
        $this->assertInstanceOf(ParameterBagInterface::class, $dellinHttpClient->parameter);
        $this->assertInstanceOf(SerializerInterface::class, $dellinHttpClient->serializer);
        $this->assertInstanceOf(CacheInterface::class, $dellinHttpClient->cache);
    }
}
