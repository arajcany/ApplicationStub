<?php
use Migrations\AbstractMigration;

class WorkersAddBackgroundServiceLink extends AbstractMigration
{

    public function up()
    {

        $this->table('workers')
            ->addColumn('background_services_link', 'string', [
                'after' => 'pid',
                'default' => null,
                'length' => 128,
                'null' => true,
            ])
            ->update();
    }

    public function down()
    {

        $this->table('workers')
            ->removeColumn('background_services_link')
            ->update();
    }
}

