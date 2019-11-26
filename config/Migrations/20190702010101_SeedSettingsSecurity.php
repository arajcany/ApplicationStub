<?php

use Migrations\AbstractMigration;

class SeedSettingsSecurity extends AbstractMigration
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
                'name' => 'Emergency Email',
                'description' => 'Emergency email address in case of lockout or catastrophic failure',
                'property_group' => 'install',
                'property_key' => 'emergency_email',
                'property_value' => '',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Use HTTPS',
                'description' => 'Force the use of HTTPS for all connections',
                'property_group' => 'install',
                'property_key' => 'security_https',
                'property_value' => 'true',
                'selections' => '{"false":"False","true":"True"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Use CSRF',
                'description' => 'Enable CSRF form protection',
                'property_group' => 'install',
                'property_key' => 'security_csrf',
                'property_value' => 'true',
                'selections' => '{"false":"False","true":"True"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Historical Data Purge',
                'description' => 'Purge data older than the specified number of months',
                'property_group' => 'archive',
                'property_key' => 'data_purge',
                'property_value' => '12',
                'selections' => '{"1":"1 Month","3":"3 Months","6":"6 Months","12":"12 Months","18":"18 Months","24":"24 Months","36":"36 Months","48":"48 Months","60":"60 Months","600":"Never"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Historical Audit/Logging Purge',
                'description' => 'Purge audit entries older than the specified number of months',
                'property_group' => 'archive',
                'property_key' => 'audit_purge',
                'property_value' => '12',
                'selections' => '{"1":"1 Month","3":"3 Months","6":"6 Months","12":"12 Months","18":"18 Months","24":"24 Months","36":"36 Months","48":"48 Months","60":"60 Months","600":"Never"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Strong Password',
                'description' => 'If the Application should use strong passwords',
                'property_group' => 'password_strong',
                'property_key' => 'password_strong_bool',
                'property_value' => 'true',
                'selections' => '{"false":"False","true":"True"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Strong Password Length',
                'description' => 'Minimum password length',
                'property_group' => 'password_strong',
                'property_key' => 'password_strong_length',
                'property_value' => '8',
                'selections' => '{"5":"5","6":"6","7":"7","8":"8","9":"9","10":"10","11":"11","12":"12"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Strong Password Lower Case',
                'description' => 'Password needs to have a lower case character',
                'property_group' => 'password_strong',
                'property_key' => 'password_strong_lower',
                'property_value' => 'true',
                'selections' => '{"false":"False","true":"True"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Strong Password Upper Case',
                'description' => 'Password needs to have a upper case character',
                'property_group' => 'password_strong',
                'property_key' => 'password_strong_upper',
                'property_value' => 'true',
                'selections' => '{"false":"False","true":"True"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Strong Password Special',
                'description' => 'Password needs to have a special character',
                'property_group' => 'password_strong',
                'property_key' => 'password_strong_special',
                'property_value' => 'true',
                'selections' => '{"false":"False","true":"True"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Strong Password Number',
                'description' => 'Password needs to have a number',
                'property_group' => 'password_strong',
                'property_key' => 'password_strong_number',
                'property_value' => 'true',
                'selections' => '{"false":"False","true":"True"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Strong Password Expiry',
                'description' => 'Force password reset in days',
                'property_group' => 'password_strong',
                'property_key' => 'password_reset_days',
                'property_value' => '365',
                'selections' => '{"30":"30 Days","60":"60 Days","90":"90 Days","365":"365 Days","5000":"Never"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Account Expiry',
                'description' => 'How long before an Account automatically expires',
                'property_group' => 'account',
                'property_key' => 'account_expiry',
                'property_value' => '5000',
                'selections' => '{"30":"30 Days","60":"60 Days","90":"90 Days","365":"365 Days","720":"720 Days","1800":"1800 Days","5000":"Never"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Account Expiry Action',
                'description' => 'What to do with expired Accounts',
                'property_group' => 'account',
                'property_key' => 'account_expiry_action',
                'property_value' => 'lock',
                'selections' => '{"delete":"Delete the Account","lock":"Lock the Account"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Self Registration Flow',
                'description' => 'Control how Users self register into the Application',
                'property_group' => 'account',
                'property_key' => 'self_registration',
                'property_value' => 'none',
                'selections' => '{
"none": "No self registration",
"self": "Grant immediate access",
"admin": "Admin approval to grant access"
}',
                'html_select_type' => 'multiple',
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
