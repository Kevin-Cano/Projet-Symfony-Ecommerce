<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class VersionXXXXXXXXXXXXXX extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename author_id to seller_id in watch table';
    }

    public function up(Schema $schema): void
    {
        // Renommer la colonne author_id en seller_id
        $this->addSql('ALTER TABLE watch CHANGE author_id seller_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE watch ADD CONSTRAINT FK_watch_seller_id FOREIGN KEY (seller_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // Revenir en arrière
        $this->addSql('ALTER TABLE watch DROP FOREIGN KEY FK_watch_seller_id');
        $this->addSql('ALTER TABLE watch CHANGE seller_id author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE watch ADD CONSTRAINT FK_watch_author_id FOREIGN KEY (author_id) REFERENCES user (id)');
    }
} 