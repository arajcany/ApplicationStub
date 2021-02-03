<?php
use Migrations\AbstractMigration;

class WorkersAddShutdown extends AbstractMigration
{

    public function up()
    {

        $this->table('workers')
            ->addColumn('force_shutdown', 'boolean', [
                'after' => 'force_retirement',
                'default' => null,
                'length' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'appointment_date',
                ],
                [
                    'name' => 'workers_appointment_date_index',
                ]
            )
            ->addIndex(
                [
                    'force_retirement',
                ],
                [
                    'name' => 'workers_force_retirement_index',
                ]
            )
            ->addIndex(
                [
                    'force_shutdown',
                ],
                [
                    'name' => 'workers_force_shutdown_index',
                ]
            )
            ->addIndex(
                [
                    'retirement_date',
                ],
                [
                    'name' => 'workers_retirement_date_index',
                ]
            )
            ->addIndex(
                [
                    'termination_date',
                ],
                [
                    'name' => 'workers_termination_date_index',
                ]
            )
            ->update();
    }

    public function down()
    {

        $this->table('workers')
            ->removeIndexByName('workers_appointment_date_index')
            ->removeIndexByName('workers_force_retirement_index')
            ->removeIndexByName('workers_force_shutdown_index')
            ->removeIndexByName('workers_retirement_date_index')
            ->removeIndexByName('workers_termination_date_index')
            ->update();

        $this->table('workers')
            ->removeColumn('force_shutdown')
            ->update();
    }
}

