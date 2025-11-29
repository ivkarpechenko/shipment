<?php

namespace App\Tests\Infrastructure\DaData\Service;

use App\Infrastructure\DaData\Exception\DaDataException;
use App\Infrastructure\DaData\Response\Denormalizer\DaDataOktmoDenormalizer;
use App\Infrastructure\DaData\Response\Dto\DaDataOktmoDto;
use App\Infrastructure\DaData\Service\FindAddressByOktmoService;
use Dadata\DadataClient;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class FindAddressByOktmoServiceTest extends TestCase
{
    public function testFindAddressByOktmoSuccess()
    {
        $oktmo = '45398000';

        $responseFromDaData = [
            [
                'data' => [
                    'oktmo' => '45311000',
                    'area_type' => '3',
                    'area_code' => '45311000',
                    'area' => 'муниципальный округ Метрогородок',
                    'subarea_type' => null,
                    'subarea_code' => null,
                    'subarea' => null,
                ],
            ],
        ];

        $expectedDto = new DaDataOktmoDto(
            oktmo: '45311000',
            areaType: '3',
            areaCode: '45311000',
            area: 'муниципальный округ Метрогородок',
            subareaType: null,
            subareaCode: null,
            subarea: null
        );

        $daDataClientMock = $this->createMock(DadataClient::class);
        $daDataClientMock
            ->expects($this->once())
            ->method('findById')
            ->with('oktmo', $oktmo)
            ->willReturn($responseFromDaData);

        $loggerMock = $this->createMock(LoggerInterface::class);

        $denormalizerMock = $this->createMock(DaDataOktmoDenormalizer::class);
        $denormalizerMock
            ->expects($this->once())
            ->method('denormalize')
            ->with($responseFromDaData[0]['data'], DaDataOktmoDto::class)
            ->willReturn($expectedDto);

        $service = new FindAddressByOktmoService($daDataClientMock, $loggerMock, $denormalizerMock);

        $result = $service->find($oktmo);

        $this->assertNotNull($result);
        $this->assertInstanceOf(DaDataOktmoDto::class, $result);
        $this->assertEquals($expectedDto, $result);
    }

    public function testFindAddressByOktmoThrowsDaDataException()
    {
        $oktmo = '45398000';

        $daDataClientMock = $this->createMock(DadataClient::class);
        $daDataClientMock
            ->expects($this->once())
            ->method('findById')
            ->with('oktmo', $oktmo)
            ->willThrowException(new DaDataException('Ошибка DaData'));

        $loggerMock = $this->createMock(LoggerInterface::class);
        $loggerMock
            ->expects($this->once())
            ->method('error')
            ->with($this->stringContains('Ошибка при работе с DaData: Ошибка DaData'));

        $denormalizerMock = $this->createMock(DaDataOktmoDenormalizer::class);

        $service = new FindAddressByOktmoService($daDataClientMock, $loggerMock, $denormalizerMock);

        $this->expectException(DaDataException::class);
        $this->expectExceptionMessage('Возникла ошибка при обращении в DaData');

        $service->find($oktmo);
    }
}
