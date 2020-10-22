<?php

use Migrations\AbstractMigration;

class WorkersAndErrands extends AbstractMigration
{

    public function up()
    {

        $this->table('errands')
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
            ->addColumn('wait_for_link', 'integer', [
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
                'limit' => 128,
                'null' => true,
            ])
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => true,
            ])
            ->addColumn('worker_link', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('worker_name', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => true,
            ])
            ->addColumn('class', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('method', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('parameters', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('status', 'string', [
                'default' => null,
                'limit' => 50,
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
            ->addColumn('progress_bar', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('priority', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('return_value', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('return_message', 'text', [
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
            ->create();

        $this->table('workers')
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
            ->addColumn('server', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => true,
            ])
            ->addColumn('domain', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => true,
            ])
            ->addColumn('name', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => true,
            ])
            ->addColumn('type', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => true,
            ])
            ->addColumn('errand_link', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('errand_name', 'string', [
                'default' => null,
                'limit' => 128,
                'null' => true,
            ])
            ->addColumn('appointment_date', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('retirement_date', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('termination_date', 'timestamp', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('force_retirement', 'boolean', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('pid', 'integer', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->seedSettings();
    }

    public function down()
    {

        $this->table('errands')->drop()->save();

        $this->table('workers')->drop()->save();
    }

    public function seedSettings()
    {
        $currentDate = gmdate("Y-m-d H:i:s");

        $data = [
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Errand Worker Limit',
                'description' => 'How many Scheduled Errand Workers can be booted at the one time',
                'property_group' => 'errand_worker',
                'property_key' => 'errand_worker_limit',
                'property_value' => '4',
                'selections' => '{"1":"1","2":"2","3":"3","4":"4","5":"5","6":"6","7":"7","8":"8","9":"9","10":"10","11":"11","12":"12"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => null
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Errand Worker Life Expectancy',
                'description' => 'How long Scheduled Errand Workers can run for till they are retired (minutes)',
                'property_group' => 'errand_worker',
                'property_key' => 'errand_worker_life_expectancy',
                'property_value' => '6',
                'selections' => '{"10":"10","11":"11","12":"12","13":"13","14":"14","15":"15","16":"16","17":"17","18":"18","19":"19","20":"20"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => null
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Errand Worker Grace Period',
                'description' => 'Grace period for a long running Errand Worker before forced termination (minutes)',
                'property_group' => 'errand_worker',
                'property_key' => 'errand_worker_grace_period',
                'property_value' => '1',
                'selections' => '{"1":"1","2":"2","3":"3","4":"4","5":"5","6":"6","7":"7","8":"8","9":"9","10":"10"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => null
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Errand Retry Limit',
                'description' => 'How many time to retry running a Errand',
                'property_group' => 'errand_worker',
                'property_key' => 'errand_retry_limit',
                'property_value' => '3',
                'selections' => '{"1":"1","2":"2","3":"3","4":"4","5":"5"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => null
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Message Worker Limit',
                'description' => 'How many Message Workers can be booted at the one time',
                'property_group' => 'message_worker',
                'property_key' => 'message_worker_limit',
                'property_value' => '4',
                'selections' => '{"1":"1","2":"2","3":"3","4":"4","5":"5","6":"6","7":"7","8":"8","9":"9","10":"10","11":"11","12":"12"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => null
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Message Worker Life Expectancy',
                'description' => 'How long Message Workers can run for till they are retired (minutes)',
                'property_group' => 'message_worker',
                'property_key' => 'message_worker_life_expectancy',
                'property_value' => '6',
                'selections' => '{"10":"10","11":"11","12":"12","13":"13","14":"14","15":"15","16":"16","17":"17","18":"18","19":"19","20":"20"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => null
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Message Worker Grace Period',
                'description' => 'Grace period for a long running Message Workers before forced termination (minutes)',
                'property_group' => 'message_worker',
                'property_key' => 'message_worker_grace_period',
                'property_value' => '1',
                'selections' => '{"1":"1","2":"2","3":"3","4":"4","5":"5","6":"6","7":"7","8":"8","9":"9","10":"10"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => null
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Message Retry Limit',
                'description' => 'How many time to retry sending a Message',
                'property_group' => 'message_worker',
                'property_key' => 'message_retry_limit',
                'property_value' => '4',
                'selections' => '{"1":"1","2":"2","3":"3","4":"4","5":"5"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => null
            ],
        ];

        if (!empty($data)) {
            $table = $this->table('settings');
            $table->insert($data)->save();
        }
    }

}


