<?php

namespace App\Tests\Domain\Directory\Service;

use App\Domain\Directory\Dto\OkatoOktmoDto;
use App\Domain\Directory\Entity\OkatoOktmo;
use App\Domain\Directory\Repository\OkatoOktmoRepositoryInterface;
use App\Domain\Directory\Service\CreateOkatoOktmoService;
use PHPUnit\Framework\TestCase;

class CreateOkatoOktmoServiceTest extends TestCase
{
    public function testCreateSkipsIfOkatoExists(): void
    {
        $existingOkato = '1234567';
        $dto = new OkatoOktmoDto($existingOkato, '12345678', 'Test Location');

        $repositoryMock = $this->createMock(OkatoOktmoRepositoryInterface::class);
        $repositoryMock
            ->expects($this->once())
            ->method('ofOkato')
            ->with($existingOkato)
            ->willReturn(new OkatoOktmo($existingOkato, '12345678', 'Existing Location'));

        $repositoryMock
            ->expects($this->never())
            ->method('create');

        $service = new CreateOkatoOktmoService($repositoryMock);
        $service->create($dto);
    }

    public function testCreateSavesNewRecordIfOkatoDoesNotExist(): void
    {
        $newOkato = '1234567';
        $dto = new OkatoOktmoDto($newOkato, '12345678', 'Test Location');

        $repositoryMock = $this->createMock(OkatoOktmoRepositoryInterface::class);
        $repositoryMock
            ->expects($this->once())
            ->method('ofOkato')
            ->with($newOkato)
            ->willReturn(null);

        $repositoryMock
            ->expects($this->once())
            ->method('create')
            ->with($this->callback(function (OkatoOktmo $entity) use ($dto) {
                return $entity->getOkato() === $dto->okato
                    && $entity->getOktmo() === $dto->oktmo
                    && $entity->getLocation() === $dto->location;
            }));

        $service = new CreateOkatoOktmoService($repositoryMock);
        $service->create($dto);
    }
}
