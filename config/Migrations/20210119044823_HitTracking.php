<?php

use Migrations\AbstractMigration;

class HitTracking extends AbstractMigration
{

    public function up()
    {

        $this->table('track_hits')
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('url', 'string', [
                'default' => null,
                'limit' => 1024,
                'null' => false,
            ])
            ->addColumn('scheme', 'string', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('host', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('port', 'string', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('path', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('query', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('app_execution_time', 'float', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('data', 'string', [
                'default' => null,
                'limit' => 2048,
                'null' => false,
            ])
            ->addIndex(
                [
                    'url',
                ]
            )
            ->addIndex(
                [
                    'scheme',
                ]
            )
            ->addIndex(
                [
                    'app_execution_time',
                ]
            )
            ->addIndex(
                [
                    'query',
                ]
            )
            ->addIndex(
                [
                    'port',
                ]
            )
            ->addIndex(
                [
                    'path',
                ]
            )
            ->addIndex(
                [
                    'host',
                ]
            )
            ->addIndex(
                [
                    'created',
                ]
            )
            ->create();

        $this->seedSettings();
    }

    public function down()
    {

        $this->table('track_hits')->drop()->save();
    }

    public function seedSettings()
    {
        $currentDate = gmdate("Y-m-d H:i:s");

        $data = [
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Enable Hit Tracking',
                'description' => 'Track hits on the Application. May have an impact on performance.',
                'property_group' => 'hit_tracking',
                'property_key' => 'hit_tracking_enabled',
                'property_value' => 'false',
                'selections' => '{"false":"False","true":"True"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => false
            ]
        ];

        if (!empty($data)) {
            $table = $this->table('settings');
            $table->insert($data)->save();
        }
    }
}

