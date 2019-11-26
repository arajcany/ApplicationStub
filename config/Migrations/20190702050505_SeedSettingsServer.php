<?php

use Migrations\AbstractMigration;

class SeedSettingsServer extends AbstractMigration
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
                'name' => 'URL Base Path',
                'description' => 'Base URL where the reports can be retrieved from',
                'property_group' => 'server',
                'property_key' => 'base_url',
                'property_value' => 'http://' . gethostname() . '/',
                'selections' => null,
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => false
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'UNC Base Path',
                'description' => 'Base UNC where the reports can be retrieved from',
                'property_group' => 'server',
                'property_key' => 'base_unc',
                'property_value' => '\\\\' . gethostname() . '\\Share_Storage\\',
                'selections' => null,
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => false
            ],
        ];

        if (!empty($data)) {
            $table = $this->table('settings');
            $table->insert($data)->save();
        }
    }
}
