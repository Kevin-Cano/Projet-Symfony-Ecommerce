<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250301202442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favorite (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, watch_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_68C58ED9A76ED395 (user_id), INDEX IDX_68C58ED9C7C58135 (watch_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, watch_id INT NOT NULL, rating INT NOT NULL, comment VARCHAR(1000) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_794381C6A76ED395 (user_id), INDEX IDX_794381C6C7C58135 (watch_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE favorite ADD CONSTRAINT FK_68C58ED9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE favorite ADD CONSTRAINT FK_68C58ED9C7C58135 FOREIGN KEY (watch_id) REFERENCES watch (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6C7C58135 FOREIGN KEY (watch_id) REFERENCES watch (id)');
        $this->addSql('ALTER TABLE watch DROP FOREIGN KEY FK_500B4A26F675F31B');
        $this->addSql('DROP INDEX IDX_500B4A26F675F31B ON watch');
        $this->addSql('ALTER TABLE watch CHANGE author_id seller_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE watch ADD CONSTRAINT FK_500B4A268DE820D9 FOREIGN KEY (seller_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_500B4A268DE820D9 ON watch (seller_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favorite DROP FOREIGN KEY FK_68C58ED9A76ED395');
        $this->addSql('ALTER TABLE favorite DROP FOREIGN KEY FK_68C58ED9C7C58135');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6A76ED395');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6C7C58135');
        $this->addSql('DROP TABLE favorite');
        $this->addSql('DROP TABLE review');
        $this->addSql('ALTER TABLE watch DROP FOREIGN KEY FK_500B4A268DE820D9');
        $this->addSql('DROP INDEX IDX_500B4A268DE820D9 ON watch');
        $this->addSql('ALTER TABLE watch CHANGE seller_id author_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE watch ADD CONSTRAINT FK_500B4A26F675F31B FOREIGN KEY (author_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_500B4A26F675F31B ON watch (author_id)');
    }
}
