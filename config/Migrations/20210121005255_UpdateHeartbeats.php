<?php
use Migrations\AbstractMigration;

class UpdateHeartbeats extends AbstractMigration
{

    public function up()
    {

        $this->table('heartbeats')
            ->addColumn('name', 'string', [
                'after' => 'pid',
                'default' => null,
                'length' => 128,
                'null' => true,
            ])
            ->addColumn('description', 'string', [
                'after' => 'name',
                'default' => null,
                'length' => 128,
                'null' => true,
            ])
            ->addIndex(
                [
                    'auto_delete',
                ],
                [
                    'name' => 'heartbeats_auto_delete_index',
                ]
            )
            ->addIndex(
                [
                    'context',
                ],
                [
                    'name' => 'heartbeats_context_index',
                ]
            )
            ->addIndex(
                [
                    'created',
                ],
                [
                    'name' => 'heartbeats_created_index',
                ]
            )
            ->addIndex(
                [
                    'description',
                ],
                [
                    'name' => 'heartbeats_description_index',
                ]
            )
            ->addIndex(
                [
                    'domain',
                ],
                [
                    'name' => 'heartbeats_domain_index',
                ]
            )
            ->addIndex(
                [
                    'expiration',
                ],
                [
                    'name' => 'heartbeats_expiration_index',
                ]
            )
            ->addIndex(
                [
                    'name',
                ],
                [
                    'name' => 'heartbeats_name_index',
                ]
            )
            ->addIndex(
                [
                    'pid',
                ],
                [
                    'name' => 'heartbeats_pid_index',
                ]
            )
            ->addIndex(
                [
                    'server',
                ],
                [
                    'name' => 'heartbeats_server_index',
                ]
            )
            ->addIndex(
                [
                    'type',
                ],
                [
                    'name' => 'heartbeats_type_index',
                ]
            )
            ->update();
    }

    public function down()
    {

        $this->table('heartbeats')
            ->removeIndexByName('heartbeats_auto_delete_index')
            ->removeIndexByName('heartbeats_context_index')
            ->removeIndexByName('heartbeats_created_index')
            ->removeIndexByName('heartbeats_description_index')
            ->removeIndexByName('heartbeats_domain_index')
            ->removeIndexByName('heartbeats_expiration_index')
            ->removeIndexByName('heartbeats_name_index')
            ->removeIndexByName('heartbeats_pid_index')
            ->removeIndexByName('heartbeats_server_index')
            ->removeIndexByName('heartbeats_type_index')
            ->update();

        $this->table('heartbeats')
            ->removeColumn('name')
            ->removeColumn('description')
            ->update();
    }
}

