<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250220090101 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE watch ADD author_id INT DEFAULT NULL, ADD publication_date DATETIME NOT NULL, ADD state VARCHAR(255) DEFAULT NULL, DROP stock, DROP is_available, CHANGE reference reference VARCHAR(255) DEFAULT NULL, CHANGE price price DOUBLE PRECISION NOT NULL, CHANGE description description VARCHAR(255) DEFAULT NULL, CHANGE movement movement VARCHAR(255) DEFAULT NULL, CHANGE material material VARCHAR(255) DEFAULT NULL, CHANGE water_resistance water_resistance VARCHAR(255) DEFAULT NULL, CHANGE bracelet bracelet VARCHAR(255) DEFAULT NULL, CHANGE image_url picture VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE watch ADD CONSTRAINT FK_500B4A26F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_500B4A26F675F31B ON watch (author_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE watch DROP FOREIGN KEY FK_500B4A26F675F31B');
        $this->addSql('DROP INDEX IDX_500B4A26F675F31B ON watch');
        $this->addSql('ALTER TABLE watch ADD stock INT NOT NULL, ADD is_available TINYINT(1) NOT NULL, DROP author_id, DROP publication_date, DROP state, CHANGE description description LONGTEXT NOT NULL, CHANGE price price VARCHAR(255) NOT NULL, CHANGE reference reference VARCHAR(255) NOT NULL, CHANGE movement movement VARCHAR(255) NOT NULL, CHANGE material material VARCHAR(255) NOT NULL, CHANGE water_resistance water_resistance VARCHAR(255) NOT NULL, CHANGE bracelet bracelet VARCHAR(255) NOT NULL, CHANGE picture image_url VARCHAR(255) NOT NULL');
    }
}
