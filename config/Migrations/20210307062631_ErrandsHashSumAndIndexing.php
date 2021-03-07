<?php
use Migrations\AbstractMigration;

class ErrandsHashSumAndIndexing extends AbstractMigration
{

    public function up()
    {

        $this->table('errands')
            ->addColumn('hash_sum', 'string', [
                'after' => 'errors_retry_limit',
                'default' => null,
                'length' => 50,
                'null' => true,
            ])
            ->addIndex(
                [
                    'activation',
                ],
                [
                    'name' => 'errands_activation_index',
                ]
            )
            ->addIndex(
                [
                    'auto_delete',
                ],
                [
                    'name' => 'errands_auto_delete_index',
                ]
            )
            ->addIndex(
                [
                    'class',
                ],
                [
                    'name' => 'errands_class_index',
                ]
            )
            ->addIndex(
                [
                    'completed',
                ],
                [
                    'name' => 'errands_completed_index',
                ]
            )
            ->addIndex(
                [
                    'created',
                ],
                [
                    'name' => 'errands_created_index',
                ]
            )
            ->addIndex(
                [
                    'domain',
                ],
                [
                    'name' => 'errands_domain_index',
                ]
            )
            ->addIndex(
                [
                    'expiration',
                ],
                [
                    'name' => 'errands_expiration_index',
                ]
            )
            ->addIndex(
                [
                    'hash_sum',
                ],
                [
                    'name' => 'errands_hash_sum_index',
                ]
            )
            ->addIndex(
                [
                    'method',
                ],
                [
                    'name' => 'errands_method_index',
                ]
            )
            ->addIndex(
                [
                    'modified',
                ],
                [
                    'name' => 'errands_modified_index',
                ]
            )
            ->addIndex(
                [
                    'name',
                ],
                [
                    'name' => 'errands_name_index',
                ]
            )
            ->addIndex(
                [
                    'parameters',
                ],
                [
                    'name' => 'errands_parameters_index',
                ]
            )
            ->addIndex(
                [
                    'priority',
                ],
                [
                    'name' => 'errands_priority_index',
                ]
            )
            ->addIndex(
                [
                    'server',
                ],
                [
                    'name' => 'errands_server_index',
                ]
            )
            ->addIndex(
                [
                    'started',
                ],
                [
                    'name' => 'errands_started_index',
                ]
            )
            ->addIndex(
                [
                    'status',
                ],
                [
                    'name' => 'errands_status_index',
                ]
            )
            ->addIndex(
                [
                    'wait_for_link',
                ],
                [
                    'name' => 'errands_wait_for_link_index',
                ]
            )
            ->update();
    }

    public function down()
    {

        $this->table('errands')
            ->removeIndexByName('errands_activation_index')
            ->removeIndexByName('errands_auto_delete_index')
            ->removeIndexByName('errands_class_index')
            ->removeIndexByName('errands_completed_index')
            ->removeIndexByName('errands_created_index')
            ->removeIndexByName('errands_domain_index')
            ->removeIndexByName('errands_expiration_index')
            ->removeIndexByName('errands_hash_sum_index')
            ->removeIndexByName('errands_method_index')
            ->removeIndexByName('errands_modified_index')
            ->removeIndexByName('errands_name_index')
            ->removeIndexByName('errands_parameters_index')
            ->removeIndexByName('errands_priority_index')
            ->removeIndexByName('errands_server_index')
            ->removeIndexByName('errands_started_index')
            ->removeIndexByName('errands_status_index')
            ->removeIndexByName('errands_wait_for_link_index')
            ->update();

        $this->table('errands')
            ->removeColumn('hash_sum')
            ->update();
    }
}

