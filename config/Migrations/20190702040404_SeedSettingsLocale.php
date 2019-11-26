<?php

use Migrations\AbstractMigration;

class SeedSettingsLocale extends AbstractMigration
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
                'name' => 'Language',
                'description' => 'Language as presented in the GUI',
                'property_group' => 'locale',
                'property_key' => 'language',
                'property_value' => 'en-AU',
                'selections' => '{"en-AU":"English - Australian"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Date Format',
                'description' => 'Format of the Date as presented in the GUI',
                'property_group' => 'locale',
                'property_key' => 'date_format',
                'property_value' => 'yyyy-MM-dd',
                'selections' => '{
"Year-Month-Day": {
"yyyy-MM-dd": "yyyy-mm-dd"
},
"Day-Month-Year": {
"dd\\/MM\\/yyyy": "dd\\/mm\\/yyyy",
"d\\/M\\/yy": "d\\/m\\/yy"
},
"Month-Day-Year": {
"MM\\/dd\\/yyyy": "mm\\/dd\\/yyyy",
"M\\/d\\/yy": "m\\/d\\/yy"
}
}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Time Format',
                'description' => 'Format of the Time as presented in the GUI',
                'property_group' => 'locale',
                'property_key' => 'time_format',
                'property_value' => 'HH:mm:ss',
                'selections' => '{
"24-Hour": {
"HH:mm:ss": "hh:mm:ss",
"H:m": "h:m"
},
"12-Hour": {
"hh:mm:ss a": "hh:mm:ss am\\/pm",
"h:m a": "h:m am\\/pm"
}
}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Date and Time Format',
                'description' => 'Format of the Date and Time as presented in the GUI',
                'property_group' => 'locale',
                'property_key' => 'datetime_format',
                'property_value' => 'yyyy-MM-dd HH:mm:ss',
                'selections' => '{
"yyyy-MM-dd HH:mm:ss": "yyyy-mm-dd hh:mm:ss",
"0": "Tuesday, April 12, 1952 AD or 3:30:42pm PST",
"1": "January 12, 1952 or 3:30:32pm",
"2": "Jan 12, 1952",
"3": "12\\/13\\/52 or 3:30pm"
}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Weeks Starts On',
                'description' => 'The first day of the week',
                'property_group' => 'locale',
                'property_key' => 'week_start',
                'property_value' => 'Sunday',
                'selections' => '{"Sunday":"Sunday","Monday":"Monday","Tuesday":"Tuesday","Wednesday":"Wednesday","Thursday":"Thursday","Friday":"Friday","Saturday":"Saturday"}',
                'html_select_type' => 'select',
                'match_pattern' => null,
                'is_masked' => '0',
            ],
            [
                'created' => $currentDate,
                'modified' => $currentDate,
                'name' => 'Timezone',
                'description' => 'Timezone of the Application',
                'property_group' => 'locale',
                'property_key' => 'timezone',
                'property_value' => 'Australia/Sydney',
                'selections' => file_get_contents(__DIR__ . "/../php_timezones.json"),
                'html_select_type' => 'select',
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
