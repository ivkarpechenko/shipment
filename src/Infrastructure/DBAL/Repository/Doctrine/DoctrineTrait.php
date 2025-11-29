<?php

namespace App\Infrastructure\DBAL\Repository\Doctrine;

use Doctrine\DBAL\Platforms\SqlitePlatform;
use Symfony\Component\Uid\Uuid;

trait DoctrineTrait
{
    public function convertUUIDsByPlatform(array $uuids): array
    {
        $databaseDriverType = $this->entityManager
            ->getConnection()
            ->getDriver()
            ->getDatabasePlatform();

        return array_map(function (Uuid $uuid) use ($databaseDriverType) {
            if ($databaseDriverType instanceof SqlitePlatform) {
                return $uuid->toBinary();
            }

            return $uuid->toRfc4122();
        }, $uuids);
    }
}
