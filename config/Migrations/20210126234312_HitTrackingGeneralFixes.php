<?php
use Migrations\AbstractMigration;

class HitTrackingGeneralFixes extends AbstractMigration
{

    public function up()
    {
        $this->table('track_hits')
            ->removeIndexByName('sqlite_autoindex_track_hits_1')
            ->removeIndexByName('track_hits_response_time_index')
            ->update();

        $this->table('track_hits')
            ->removeColumn('response_time')
            ->update();

        $this->table('track_hits')
            ->addColumn('app_execution_time', 'float', [
                'after' => 'query',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'app_execution_time',
                ],
                [
                    'name' => 'track_hits_response_time_index',
                ]
            )
            ->update();
    }

    public function down()
    {

        $this->table('track_hits')
            ->removeIndexByName('track_hits_response_time_index')
            ->update();

        $this->table('track_hits')
            ->addColumn('response_time', 'float', [
                'after' => 'query',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->removeColumn('app_execution_time')
            ->addIndex(
                [
                    'id',
                ],
                [
                    'name' => 'sqlite_autoindex_track_hits_1',
                    'unique' => true,
                ]
            )
            ->addIndex(
                [
                    'response_time',
                ],
                [
                    'name' => 'track_hits_response_time_index',
                ]
            )
            ->update();
    }
}

