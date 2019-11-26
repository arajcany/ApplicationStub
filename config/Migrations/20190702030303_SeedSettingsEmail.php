<?php

use Migrations\AbstractMigration;

class SeedSettingsEmail extends AbstractMigration
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
                'name' => 'Email SMTP Server',
                'description' => 'The SMTP server used to send emails',
                'property_group' => 'email_server',
                'property_key' => 'email_smtp_host',
                'property_value' => '127.0.0.1',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Email SMTP Server Port',
                'description' => 'The Port to use on the SMTP server',
                'property_group' => 'email_server',
                'property_key' => 'email_smtp_port',
                'property_value' => '25',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Email TLS',
                'description' => 'Use TLS Security when sending emails',
                'property_group' => 'email_server',
                'property_key' => 'email_tls',
                'property_value' => 'false',
                'selections' => '{"false":"False","true":"True"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Email Timeout',
                'description' => 'Timeout when sending emails',
                'property_group' => 'email_server',
                'property_key' => 'email_timeout',
                'property_value' => '10',
                'selections' => '{"5":"5","6":"6","7":"7","8":"8","9":"9","10":"10","11":"11","12":"12","13":"13","14":"14","15":"15","20":"20","30":"30"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Email Username',
                'description' => 'If the SMTP server requires a username and password',
                'property_group' => 'email_server',
                'property_key' => 'email_username',
                'property_value' => '',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Email Password',
                'description' => 'If the SMTP server requires a username and password',
                'property_group' => 'email_server',
                'property_key' => 'email_password',
                'property_value' => '',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => '1',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Email from Name',
                'description' => 'The Application will use this name when sending emails',
                'property_group' => 'email_server',
                'property_key' => 'email_from_name',
                'property_value' => 'Application Stub',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Email from Address',
                'description' => 'The Application will use this email when sending emails',
                'property_group' => 'email_server',
                'property_key' => 'email_from_address',
                'property_value' => 'app@localhost.com',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Email to Address',
                'description' => 'The default to when sending emails',
                'property_group' => 'email_server',
                'property_key' => 'email_to_address',
                'property_value' => 'to@example.com',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Email CC Address',
                'description' => 'The default CC when sending emails',
                'property_group' => 'email_server',
                'property_key' => 'email_cc_address',
                'property_value' => 'cc@example.com',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Email BCC Address',
                'description' => 'The default BCC when sending emails',
                'property_group' => 'email_server',
                'property_key' => 'email_bcc_address',
                'property_value' => 'bcc@example.com',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
        ];

        if (!empty($data)) {
            $table = $this->table('settings');
            $table->insert($data)->save();
        }
    }
}
