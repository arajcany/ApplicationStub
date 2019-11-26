<?php
use Migrations\AbstractMigration;

class SecurityTokens extends AbstractMigration
{

    public function up()
    {

        $this->table('seeds')
            ->addColumn('created', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('activation', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('expiration', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('token', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('url', 'string', [
                'default' => null,
                'limit' => 1024,
                'null' => true,
            ])
            ->addColumn('bids', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('bid_limit', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('user_link', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->create();
    }

    public function down()
    {

        $this->table('seeds')->drop()->save();
    }
}

