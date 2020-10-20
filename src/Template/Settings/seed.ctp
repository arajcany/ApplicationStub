<?php
/**
 * @var Cake\ORM\Query $seeds ;
 */

use App\Utility\Feedback\DebugCapture;

$seedCompiled = [];
foreach ($seeds as $k => $seed) {

    foreach ($seed as $key => $value) {

        if ($key == 'id') {
            //do nothing
        } elseif ($key == 'created') {
            /**
             * @var Cake\I18n\FrozenTime $value
             */
            //$seedCompiled[$k][$key] = $value->i18nFormat("yyyy-MM-dd HH:mm:ss");
            $seedCompiled[$k][$key] = "\$currentDate";
        } elseif ($key == 'modified') {
            /**
             * @var Cake\I18n\FrozenTime $value
             */
            //$seedCompiled[$k][$key] = $value->i18nFormat("yyyy-MM-dd HH:mm:ss");
            $seedCompiled[$k][$key] = "\$currentDate";
        } else {
            $seedCompiled[$k][$key] = $value;
        }
    }

}

$count = count($seedCompiled);

$output = DebugCapture::captureDump($seedCompiled);
$output = str_replace("'\$currentDate'", "\$currentDate", $output);
foreach (range(0, $count - 1) as $key) {
    $output = str_replace("(int) $key => ", "", $output);
}

echo "<pre>";
echo $output;
echo "</pre>";
