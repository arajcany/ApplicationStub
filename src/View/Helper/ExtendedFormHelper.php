<?php

namespace App\View\Helper;

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

}
