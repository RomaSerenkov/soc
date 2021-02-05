<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210122065419 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Add user table fields birthday, image';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE user ADD birthday DATE NULL, ADD image VARCHAR(255) NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE user DROP birthday, DROP image');
    }
}
