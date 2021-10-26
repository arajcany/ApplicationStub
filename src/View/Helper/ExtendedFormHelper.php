<?php

namespace App\View\Helper;

use Cake\I18n\FrozenTime;
use Cake\View\Helper\FormHelper as CakeFormHelper;

/**
 * Form helper
 */
class ExtendedFormHelper extends CakeFormHelper
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $templates = [
            'error' => '<div class="alert text-danger">{{content}}</div>',
        ];
        $this->setTemplates($templates);
    }

    /**
     * @param $currentOptions
     * @param \App\Model\Entity\Setting $setting
     * @return array
     */
    public function settingsFormatOptions($currentOptions, $setting)
    {
        $opts = [
            'id' => 'setting_' . $setting->property_key,
            'name' => '' . $setting->property_key,
            'select' => ['class' => "form-control"],
            'options' => null,
            'multiple' => false,
            'size' => 1,
            'type' => 'text',
            'required' => false,
            'disabled' => false,
            'hiddenField' => false,
            'label' => ['text' => $setting->name],
            'templateVars' => ['help' => $setting->description],
            'value' => $setting->property_value,

        ];

        $selectOpts = json_decode($setting->selections, JSON_FORCE_OBJECT);
        if ($setting->property_key == 'datetime_format') {
            $dtObj = new FrozenTime('now', TZ);
            foreach ($selectOpts as $k => $selectOpt) {
                $selectOpts[$k] = $dtObj->i18nFormat($k);
            }
        }

        if ($setting->html_select_type === 'number') {
            $opts['type'] = 'number';
            if (isset($selectOpt['min'])) {
                $opts['min'] = $selectOpt['min'];
            }
            if (isset($selectOpt['max'])) {
                $opts['max'] = $selectOpt['max'];
            }
        } elseif ($setting->html_select_type == 'multiple') {
            $opts['multiple'] = true;
            $opts['size'] = count($selectOpts);
            $opts['type'] = null;
            $opts['options'] = $selectOpts;
        } elseif ($setting->html_select_type == 'select') {
            $opts['size'] = 1;
            $opts['type'] = 'select';
            $opts['options'] = $selectOpts;
        }

        if ($setting->is_masked == true) {
            $opts['type'] = 'password';
        }

        return array_merge($currentOptions, $opts);
    }

}
