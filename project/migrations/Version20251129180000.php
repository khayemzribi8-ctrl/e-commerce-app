<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251129180000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create banner_image table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE banner_image (id INT AUTO_INCREMENT NOT NULL, image_path VARCHAR(255) NOT NULL, title VARCHAR(500), description LONGTEXT, button_text VARCHAR(255), button_url VARCHAR(255), created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE banner_image');
    }
}
