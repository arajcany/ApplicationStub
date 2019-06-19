<?php
/**
 * @var Cake\ORM\Query $seeds ;
 */

$seedCompiled = [];
foreach ($seeds as $k => $seed) {

    foreach ($seed as $key => $value) {

        if ($key == 'id') {
            //do nothing
        } elseif ($key == 'created') {
            /**
             * @var Cake\I18n\FrozenTime $value
             */
            $seedCompiled[$k][$key] = $value->i18nFormat("yyyy-MM-dd HH:mm:ss");
        } elseif ($key == 'modified') {
            /**
             * @var Cake\I18n\FrozenTime $value
             */
            $seedCompiled[$k][$key] = $value->i18nFormat("yyyy-MM-dd HH:mm:ss");
        } else {
            $seedCompiled[$k][$key] = $value;
        }
    }

}

debug($seedCompiled);