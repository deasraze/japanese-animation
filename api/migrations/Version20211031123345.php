<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211031123345 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auth_user_networks (id UUID NOT NULL, user_id UUID NOT NULL, network_name VARCHAR(16) NOT NULL, network_identity VARCHAR(16) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3EA78C3BA76ED395 ON auth_user_networks (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3EA78C3B257EBD71C756D255 ON auth_user_networks (network_name, network_identity)');
        $this->addSql('COMMENT ON COLUMN auth_user_networks.user_id IS \'(DC2Type:auth_user_id)\'');
        $this->addSql('ALTER TABLE auth_user_networks ADD CONSTRAINT FK_3EA78C3BA76ED395 FOREIGN KEY (user_id) REFERENCES auth_users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE auth_user_networks');
    }
}
