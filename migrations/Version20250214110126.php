<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250214110126 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("
            INSERT INTO delivery_method_delivery_service(delivery_service_id, delivery_method_id)
            values (
                    (select id from delivery_service where code = 'dellin'),
                    (select id from delivery_method where code = 'pvz')
                )
        ");
        $this->addSql("
            INSERT INTO public.tariff_plan
                (id,delivery_service_id, code, name, is_active, created_at, updated_at, delivery_method_id)
            VALUES(
                '90f9501f-5535-400e-b90f-d9741a7f6bb0'::uuid,
                (select id from delivery_service where code = 'dellin'), 
                'auto', 
                'ПВЗ.Деловые линии', 
                true, 
                current_timestamp,
                NULL, 
                (select id from delivery_method where code = 'pvz')
            );
        ");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("
            DELETE FROM delivery_method_delivery_service 
                   where delivery_service_id = (select id from delivery_service where code = 'dellin')
                    and delivery_method_id = (select id from delivery_method where code = 'pvz')
        ");

        $this->addSql("DELETE FROM tariff_plan where id = '90f9501f-5535-400e-b90f-d9741a7f6bb0'");
    }
}
