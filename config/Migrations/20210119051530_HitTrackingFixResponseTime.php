<?php
use Migrations\AbstractMigration;

class HitTrackingFixResponseTime extends AbstractMigration
{

    public function up()
    {

        $this->table('track_hits')
            ->changeColumn('response_time', 'decimal', [
                'default' => null,
                'length' => null,
                'limit' => null,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('track_hits')
            ->changeColumn('response_time', 'string', [
                'default' => null,
                'length' => 10,
                'null' => true,
            ])
            ->update();
    }
}

