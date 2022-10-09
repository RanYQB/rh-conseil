?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221002102020 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offer ADD city_id INT NOT NULL, DROP location');
        $this->addSql('ALTER TABLE offer ADD CONSTRAINT FK_29D6873E8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_29D6873E8BAC62AF ON offer (city_id)');
        $this->addSql('ALTER TABLE recruiter ADD city_id INT NOT NULL, DROP zipcode, DROP city');
        $this->addSql('ALTER TABLE recruiter ADD CONSTRAINT FK_DE8633D88BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CREATE INDEX IDX_DE8633D88BAC62AF ON recruiter (city_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offer DROP FOREIGN KEY FK_29D6873E8BAC62AF');
        $this->addSql('DROP INDEX IDX_29D6873E8BAC62AF ON offer');
        $this->addSql('ALTER TABLE offer ADD location VARCHAR(150) NOT NULL, DROP city_id');
        $this->addSql('ALTER TABLE recruiter DROP FOREIGN KEY FK_DE8633D88BAC62AF');
        $this->addSql('DROP INDEX IDX_DE8633D88BAC62AF ON recruiter');
        $this->addSql('ALTER TABLE recruiter ADD zipcode VARCHAR(5) NOT NULL, ADD city VARCHAR(150) NOT NULL, DROP city_id');
    }
}
