<?php

use Migrations\AbstractMigration;

class RemoteUpdate extends AbstractMigration
{

    public function up()
    {
        $this->seedSettings();
    }

    public function down()
    {
    }

    public function seedSettings()
    {
        $currentDate = gmdate("Y-m-d H:i:s");

        $data = [
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Remote Update Base URL',
                'description' => 'The base URL of the remote update files',
                'property_group' => 'remote_update',
                'property_key' => 'remote_update_url',
                'property_value' => 'http://localhost/update/',
                'selections' => '',
                'html_select_type' => 'text',
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

