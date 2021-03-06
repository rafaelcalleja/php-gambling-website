<?php

namespace Gambling\Identity\Port\Adapter\Persistence\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

final class Version20170526204325 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('user');

        $table->addColumn('id', 'uuid_binary_ordered_time');
        $table->addColumn('version', 'integer', ['default' => 1]);
        $table->addColumn('is_signed_up', 'boolean');
        $table->addColumn('credentials_username', 'string', ['notNull' => false]);
        $table->addColumn('credentials_password', 'string', ['notNull' => false]);

        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['credentials_username']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('user');
    }
}
