<?php

use Migrations\AbstractMigration;

class SeedSettingsCompanyDetails extends AbstractMigration
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
                'name' => 'Company Name',
                'description' => 'Name of the Company',
                'property_group' => 'company',
                'property_key' => 'company_name',
                'property_value' => '',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Company Address 1',
                'description' => 'Address 1 of the Company',
                'property_group' => 'company',
                'property_key' => 'company_address_1',
                'property_value' => '',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Company Address 2',
                'description' => 'Address 2 of the Company',
                'property_group' => 'company',
                'property_key' => 'company_address_2',
                'property_value' => '',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Company Suburb',
                'description' => 'Suburb of the Company',
                'property_group' => 'company',
                'property_key' => 'company_suburb',
                'property_value' => '',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Company State',
                'description' => 'State of the Company',
                'property_group' => 'company',
                'property_key' => 'company_state',
                'property_value' => '',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Company Postcode',
                'description' => 'Postcode of the Company',
                'property_group' => 'company',
                'property_key' => 'company_postcode',
                'property_value' => '',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Company Phone',
                'description' => 'Phone Number of the Company',
                'property_group' => 'company',
                'property_key' => 'company_phone',
                'property_value' => '',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Company Email',
                'description' => 'Email of the Company',
                'property_group' => 'company',
                'property_key' => 'company_email',
                'property_value' => '',
                'selections' => '',
                'html_select_type' => 'text',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Company Web Address',
                'description' => 'Web Address of the Company',
                'property_group' => 'company',
                'property_key' => 'company_web_address',
                'property_value' => '',
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
