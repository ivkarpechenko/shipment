<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240708232149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE package_product DROP CONSTRAINT FK_5C116121F44CABFF');
        $this->addSql('ALTER TABLE package_product DROP CONSTRAINT FK_5C1161214584665A');
        $this->addSql('ALTER TABLE package_product DROP CONSTRAINT package_product_pkey');
        $this->addSql('ALTER TABLE package_product ADD id UUID NOT NULL');
        $this->addSql('ALTER TABLE package_product ADD quantity INT NOT NULL');
        $this->addSql('ALTER TABLE package_product ALTER package_id DROP NOT NULL');
        $this->addSql('ALTER TABLE package_product ALTER product_id DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN package_product.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE package_product ADD CONSTRAINT FK_5C116121F44CABFF FOREIGN KEY (package_id) REFERENCES package (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE package_product ADD CONSTRAINT FK_5C1161214584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE package_product ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE package_product DROP CONSTRAINT fk_5c116121f44cabff');
        $this->addSql('ALTER TABLE package_product DROP CONSTRAINT fk_5c1161214584665a');
        $this->addSql('DROP INDEX package_product_pkey');
        $this->addSql('ALTER TABLE package_product DROP id');
        $this->addSql('ALTER TABLE package_product DROP quantity');
        $this->addSql('ALTER TABLE package_product ALTER package_id SET NOT NULL');
        $this->addSql('ALTER TABLE package_product ALTER product_id SET NOT NULL');
        $this->addSql('ALTER TABLE package_product ADD CONSTRAINT fk_5c116121f44cabff FOREIGN KEY (package_id) REFERENCES package (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE package_product ADD CONSTRAINT fk_5c1161214584665a FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE package_product ADD PRIMARY KEY (package_id, product_id)');
    }
}
