<?php
use Migrations\AbstractMigration;

class MessagesTables extends AbstractMigration
{

    public function up()
    {

        $this->table('message_beacons')
            ->addColumn('created', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('beacon_hash', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('beacon_url', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('beacon_data', 'string', [
                'default' => null,
                'limit' => 2048,
                'null' => true,
            ])
            ->addIndex(
                [
                    'modified',
                ]
            )
            ->addIndex(
                [
                    'created',
                ]
            )
            ->addIndex(
                [
                    'beacon_hash',
                ]
            )
            ->create();

        $this->table('messages')
            ->addColumn('created', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('type', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => true,
            ])
            ->addColumn('description', 'string', [
                'default' => null,
                'limit' => 1024,
                'null' => true,
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
            ->addColumn('auto_delete', 'boolean', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('started', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('completed', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('server', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => true,
            ])
            ->addColumn('domain', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('transport', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('profile', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('layout', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('template', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('email_format', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('sender', 'string', [
                'default' => null,
                'limit' => 1024,
                'null' => true,
            ])
            ->addColumn('email_from', 'string', [
                'default' => null,
                'limit' => 1024,
                'null' => true,
            ])
            ->addColumn('email_to', 'string', [
                'default' => null,
                'limit' => 1024,
                'null' => true,
            ])
            ->addColumn('email_cc', 'string', [
                'default' => null,
                'limit' => 1024,
                'null' => true,
            ])
            ->addColumn('email_bcc', 'string', [
                'default' => null,
                'limit' => 1024,
                'null' => true,
            ])
            ->addColumn('reply_to', 'string', [
                'default' => null,
                'limit' => 1024,
                'null' => true,
            ])
            ->addColumn('read_receipt', 'string', [
                'default' => null,
                'limit' => 1024,
                'null' => true,
            ])
            ->addColumn('subject', 'string', [
                'default' => null,
                'limit' => 1024,
                'null' => true,
            ])
            ->addColumn('view_vars', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('priority', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('headers', 'string', [
                'default' => null,
                'limit' => 2048,
                'null' => true,
            ])
            ->addColumn('smtp_code', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('smtp_message', 'string', [
                'default' => null,
                'limit' => 2048,
                'null' => true,
            ])
            ->addColumn('lock_code', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('errors_thrown', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('errors_retry', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('errors_retry_limit', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('beacon_hash', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('hash_sum', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addIndex(
                [
                    'type',
                ]
            )
            ->addIndex(
                [
                    'subject',
                ]
            )
            ->addIndex(
                [
                    'started',
                ]
            )
            ->addIndex(
                [
                    'server',
                ]
            )
            ->addIndex(
                [
                    'sender',
                ]
            )
            ->addIndex(
                [
                    'priority',
                ]
            )
            ->addIndex(
                [
                    'name',
                ]
            )
            ->addIndex(
                [
                    'modified',
                ]
            )
            ->addIndex(
                [
                    'lock_code',
                ]
            )
            ->addIndex(
                [
                    'hash_sum',
                ]
            )
            ->addIndex(
                [
                    'expiration',
                ]
            )
            ->addIndex(
                [
                    'email_to',
                ]
            )
            ->addIndex(
                [
                    'domain',
                ]
            )
            ->addIndex(
                [
                    'created',
                ]
            )
            ->addIndex(
                [
                    'completed',
                ]
            )
            ->addIndex(
                [
                    'beacon_hash',
                ]
            )
            ->addIndex(
                [
                    'auto_delete',
                ]
            )
            ->addIndex(
                [
                    'activation',
                ]
            )
            ->create();
    }

    public function down()
    {

        $this->table('message_beacons')->drop()->save();

        $this->table('messages')->drop()->save();
    }
}

