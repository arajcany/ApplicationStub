<?php

use Migrations\AbstractMigration;

class SeedSettingsArtifactsRepo extends AbstractMigration
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
                'name' => 'Repository UNC Path',
                'description' => 'The UNC path to the repository of data files for the App',
                'property_group' => 'repository',
                'property_key' => 'repo_unc',
                'property_value' => '\\\\localhost\\share\\repository',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => false
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Repository Base URL',
                'description' => 'The base URL of the repository',
                'property_group' => 'repository',
                'property_key' => 'repo_url',
                'property_value' => 'http:\\\\localhost\\repository',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => false
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Repository Data Purge',
                'description' => 'Purge repository data older than the specified number of months',
                'property_group' => 'repository',
                'property_key' => 'repo_purge',
                'property_value' => '12',
                'selections' => '{"3":"3 Months","6":"6 Months","12":"12 Months","18":"18 Months","24":"24 Months","36":"36 Months","48":"48 Months","60":"60 Months","600":"Never"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => false
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Repository Data Purge Interval',
                'description' => 'How often in minutes to purge the Artifacts table',
                'property_group' => 'repository',
                'property_key' => 'repo_purge_interval',
                'property_value' => '10',
                'selections' => '{"5":"5","6":"6","7":"7","8":"8","9":"9","10":"10","11":"11","12":"12","13":"13","14":"14","15":"15","20":"20","30":"30"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => null
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Repository Data Purge Limit',
                'description' => 'How many threads as Background Services',
                'property_group' => 'repository',
                'property_key' => 'repo_purge_limit',
                'property_value' => '1',
                'selections' => '{"1":"1","2":"2","3":"3","4":"4","5":"5"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => null
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Repository Artifact Serving',
                'description' => 'How the Repository serves Artifacts - Static or Dynamic',
                'property_group' => 'repository',
                'property_key' => 'repo_mode',
                'property_value' => 'static',
                'selections' => '{"static":"Best for speed. Artifacts are served via a Static URL. Anyone with the bare URL can access the Artifact.","dynamic":"Best for security. Images will be served via a dynamic URL. Only authenticated Users can access the URL."}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => false
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Repository sFTP Host',
                'description' => 'sFTP Hostname of the repository',
                'property_group' => 'repository',
                'property_key' => 'repo_sftp_host',
                'property_value' => 'localhost',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => false
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Repository sFTP Port',
                'description' => 'sFTP Port for the repository',
                'property_group' => 'repository',
                'property_key' => 'repo_sftp_port',
                'property_value' => '22',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => false
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Repository sFTP Username',
                'description' => 'sFTP Username for the repository',
                'property_group' => 'repository',
                'property_key' => 'repo_sftp_username',
                'property_value' => '',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => false
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Repository sFTP Password',
                'description' => 'sFTP Password for the repository',
                'property_group' => 'repository',
                'property_key' => 'repo_sftp_password',
                'property_value' => '',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => true
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Repository sFTP Timeout',
                'description' => 'sFTP Timeout setting for the repository',
                'property_group' => 'repository',
                'property_key' => 'repo_sftp_timeout',
                'property_value' => '2',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => false
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Repository sFTP Path',
                'description' => 'sFTP Home Path for writing/reading files',
                'property_group' => 'repository',
                'property_key' => 'repo_sftp_path',
                'property_value' => '',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => false
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Repository Icon Size',
                'description' => 'Size in pixels when delivering an Icon sized image',
                'property_group' => 'repository',
                'property_key' => 'repo_size_icon',
                'property_value' => '128',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => false
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Repository Thumbnail Size',
                'description' => 'Size in pixels when delivering a Thumbnail sized image',
                'property_group' => 'repository',
                'property_key' => 'repo_size_thumbnail',
                'property_value' => '256',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => false
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Repository Preview Size',
                'description' => 'Size in pixels when delivering a Preview sized image',
                'property_group' => 'repository',
                'property_key' => 'repo_size_preview',
                'property_value' => '512',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => false
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Repository Low Resolution Size',
                'description' => 'Size in pixels when delivering a Low Resolution sized image',
                'property_group' => 'repository',
                'property_key' => 'repo_size_lr',
                'property_value' => '1024',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => false
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Repository Medium Resolution Size',
                'description' => 'Size in pixels when delivering a Medium Resolution sized image',
                'property_group' => 'repository',
                'property_key' => 'repo_size_mr',
                'property_value' => '1600',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => false
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Repository High Resolution Size',
                'description' => 'Size in pixels when delivering a High Resolution sized image',
                'property_group' => 'repository',
                'property_key' => 'repo_size_hr',
                'property_value' => '2400',
                'selections' => '',
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
