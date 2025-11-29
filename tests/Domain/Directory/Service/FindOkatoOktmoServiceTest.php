<?php

namespace App\Tests\Domain\Directory\Service;

use App\Domain\Directory\Entity\OkatoOktmo;
use App\Domain\Directory\Repository\OkatoOktmoRepositoryInterface;
use App\Domain\Directory\Service\FindOkatoOktmoService;
use PHPUnit\Framework\TestCase;

class FindOkatoOktmoServiceTest extends TestCase
{
    public function testFindOkatoOktmoExists(): void
    {
        $okatoRepositoryMock = $this->createMock(OkatoOktmoRepositoryInterface::class);

        $mockOkatoOktmo = new OkatoOktmo('12345678', '00123456789', 'Test Location');

        $okatoRepositoryMock->method('ofOkato')
            ->with('12345678')
            ->willReturn($mockOkatoOktmo);

        $service = new FindOkatoOktmoService($okatoRepositoryMock);

        $result = $service->ofOkato('12345678');
        $this->assertNotNull($result);
        $this->assertInstanceOf(OkatoOktmo::class, $result);
        $this->assertEquals('12345678', $result->getOkato());
        $this->assertEquals('00123456789', $result->getOktmo());
        $this->assertEquals('Test Location', $result->getLocation());
    }

    public function testFindOkatoOktmoNotExists(): void
    {
        $okatoRepositoryMock = $this->createMock(OkatoOktmoRepositoryInterface::class);

        $okatoRepositoryMock->method('ofOkato')
            ->with('12345678')
            ->willReturn(null);

        $service = new FindOkatoOktmoService($okatoRepositoryMock);

        $result = $service->ofOkato('12345678');
        $this->assertNull($result);
    }
}
