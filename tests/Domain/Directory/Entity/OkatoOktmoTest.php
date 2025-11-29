<?php

namespace App\Tests\Domain\Directory\Entity;

use App\Domain\Directory\Entity\OkatoOktmo;
use PHPUnit\Framework\TestCase;

class OkatoOktmoTest extends TestCase
{
    public function testCreateOkatoOktmo()
    {
        $okato = '45297565000';
        $oktmo = '45297565001';
        $location = 'Москва';

        $okatoOktmo = new OkatoOktmo($okato, $oktmo, $location);

        $this->assertInstanceOf(OkatoOktmo::class, $okatoOktmo);

        $this->assertIsString($okatoOktmo->getOkato());
        $this->assertEquals($okato, $okatoOktmo->getOkato());

        $this->assertIsString($okatoOktmo->getOktmo());
        $this->assertEquals($oktmo, $okatoOktmo->getOktmo());

        $this->assertIsString($okatoOktmo->getLocation());
        $this->assertEquals($location, $okatoOktmo->getLocation());
    }
}
