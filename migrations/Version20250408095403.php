<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250408095403 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE address (id INT AUTO_INCREMENT NOT NULL, street VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, zip_code VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, labe VARCHAR(255) DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE coupon (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, order_id INT DEFAULT NULL, code VARCHAR(255) NOT NULL, discount_type VARCHAR(255) NOT NULL, value DOUBLE PRECISION NOT NULL, expiration_date DATETIME NOT NULL, usage_limit INT DEFAULT NULL, INDEX IDX_64BF3F02A76ED395 (user_id), INDEX IDX_64BF3F028D9F6D38 (order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, channel VARCHAR(255) NOT NULL, create_at DATETIME DEFAULT NULL, INDEX IDX_BF5476CAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE plan (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, price DOUBLE PRECISION NOT NULL, duration INT NOT NULL, max_products INT NOT NULL, benefits JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE refund_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, reason LONGTEXT NOT NULL, status VARCHAR(255) NOT NULL, message LONGTEXT DEFAULT NULL, create_at DATETIME NOT NULL, INDEX IDX_652005DBA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE review_report (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, rating_id INT NOT NULL, reason VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, create_at DATETIME NOT NULL, INDEX IDX_4E593044A76ED395 (user_id), INDEX IDX_4E593044A32EFC6 (rating_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_address (user_id INT NOT NULL, address_id INT NOT NULL, INDEX IDX_5543718BA76ED395 (user_id), INDEX IDX_5543718BF5B7AF75 (address_id), PRIMARY KEY(user_id, address_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE coupon ADD CONSTRAINT FK_64BF3F02A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE coupon ADD CONSTRAINT FK_64BF3F028D9F6D38 FOREIGN KEY (order_id) REFERENCES `order` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE refund_request ADD CONSTRAINT FK_652005DBA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE review_report ADD CONSTRAINT FK_4E593044A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE review_report ADD CONSTRAINT FK_4E593044A32EFC6 FOREIGN KEY (rating_id) REFERENCES rating (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_address ADD CONSTRAINT FK_5543718BA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_address ADD CONSTRAINT FK_5543718BF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription CHANGE utilisateur_id utilisateur_id INT NOT NULL, CHANGE star_date_at star_date_at DATETIME NOT NULL, CHANGE end_date_at end_date_at DATETIME NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP is_active, DROP firstname, DROP lastname
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE coupon DROP FOREIGN KEY FK_64BF3F02A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE coupon DROP FOREIGN KEY FK_64BF3F028D9F6D38
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE refund_request DROP FOREIGN KEY FK_652005DBA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE review_report DROP FOREIGN KEY FK_4E593044A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE review_report DROP FOREIGN KEY FK_4E593044A32EFC6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_address DROP FOREIGN KEY FK_5543718BA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_address DROP FOREIGN KEY FK_5543718BF5B7AF75
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE address
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE coupon
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE notification
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE plan
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE refund_request
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE review_report
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_address
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription CHANGE utilisateur_id utilisateur_id INT DEFAULT NULL, CHANGE star_date_at star_date_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE end_date_at end_date_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE `user` ADD is_active TINYINT(1) NOT NULL, ADD firstname VARCHAR(255) NOT NULL, ADD lastname VARCHAR(255) NOT NULL
        SQL);
    }
}
