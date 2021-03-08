<?php
use Migrations\AbstractMigration;

class ArtifactsHashSumAndIndexing extends AbstractMigration
{

    public function up()
    {

        $this->table('artifacts')
            ->addColumn('hash_sum', 'string', [
                'after' => 'unc',
                'default' => null,
                'length' => 50,
                'null' => true,
            ])
            ->addIndex(
                [
                    'activation',
                ],
                [
                    'name' => 'artifacts_activation_index',
                ]
            )
            ->addIndex(
                [
                    'created',
                ],
                [
                    'name' => 'artifacts_created_index',
                ]
            )
            ->addIndex(
                [
                    'expiration',
                ],
                [
                    'name' => 'artifacts_expiration_index',
                ]
            )
            ->addIndex(
                [
                    'hash_sum',
                ],
                [
                    'name' => 'artifacts_hash_sum_index',
                ]
            )
            ->addIndex(
                [
                    'mime_type',
                ],
                [
                    'name' => 'artifacts_mime_type_index',
                ]
            )
            ->addIndex(
                [
                    'modified',
                ],
                [
                    'name' => 'artifacts_modified_index',
                ]
            )
            ->addIndex(
                [
                    'name',
                ],
                [
                    'name' => 'artifacts_name_index',
                ]
            )
            ->addIndex(
                [
                    'token',
                ],
                [
                    'name' => 'artifacts_token_index',
                ]
            )
            ->update();
    }

    public function down()
    {

        $this->table('artifacts')
            ->removeIndexByName('artifacts_activation_index')
            ->removeIndexByName('artifacts_created_index')
            ->removeIndexByName('artifacts_expiration_index')
            ->removeIndexByName('artifacts_hash_sum_index')
            ->removeIndexByName('artifacts_mime_type_index')
            ->removeIndexByName('artifacts_modified_index')
            ->removeIndexByName('artifacts_name_index')
            ->removeIndexByName('artifacts_token_index')
            ->update();

        $this->table('artifacts')
            ->removeColumn('hash_sum')
            ->update();
    }
}

