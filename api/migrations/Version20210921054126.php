<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210921054126 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auth_user (id UUID NOT NULL, date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, email VARCHAR(255) NOT NULL, status VARCHAR(16) NOT NULL, password_hash VARCHAR(255) DEFAULT NULL, new_email VARCHAR(255) DEFAULT NULL, role VARCHAR(16) NOT NULL, name_nickname VARCHAR(20) NOT NULL, join_confirm_token_value VARCHAR(255) DEFAULT NULL, join_confirm_token_expires TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, reset_password_token_value VARCHAR(255) DEFAULT NULL, reset_password_token_expires TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, new_email_token_value VARCHAR(255) DEFAULT NULL, new_email_token_expires TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A3B536FDE7927C74 ON auth_user (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A3B536FD8D590BAA ON auth_user (name_nickname)');
        $this->addSql('COMMENT ON COLUMN auth_user.id IS \'(DC2Type:auth_user_id)\'');
        $this->addSql('COMMENT ON COLUMN auth_user.date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN auth_user.email IS \'(DC2Type:auth_user_email)\'');
        $this->addSql('COMMENT ON COLUMN auth_user.status IS \'(DC2Type:auth_user_status)\'');
        $this->addSql('COMMENT ON COLUMN auth_user.new_email IS \'(DC2Type:auth_user_email)\'');
        $this->addSql('COMMENT ON COLUMN auth_user.role IS \'(DC2Type:auth_user_role)\'');
        $this->addSql('COMMENT ON COLUMN auth_user.join_confirm_token_expires IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN auth_user.reset_password_token_expires IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN auth_user.new_email_token_expires IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE auth_user');
    }
}
