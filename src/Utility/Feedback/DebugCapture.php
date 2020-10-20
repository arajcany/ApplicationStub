<?php

namespace App\Utility\Feedback;

use Cake\Error\Debugger;

class DebugCapture extends Debugger
{
    public static function captureDump($var, $depth = 10)
    {
        return static::exportVar($var, $depth);
    }

    public static function pr($var)
    {
        $template = (PHP_SAPI !== 'cli' && PHP_SAPI !== 'phpdbg') ? '<pre class="pr">%s</pre>' : "\n%s\n\n";
        ob_start();
        printf($template, trim(print_r($var, true)));
        $data = ob_get_contents();
        ob_end_clean();

        return $data;
    }

    public static function pj($var, $depth = 10)
    {
        $template = (PHP_SAPI !== 'cli' && PHP_SAPI !== 'phpdbg') ? '<pre class="pj">%s</pre>' : "\n%s\n\n";
        ob_start();
        printf($template, trim(json_encode($var, JSON_PRETTY_PRINT)));
        $data = ob_get_contents();
        ob_end_clean();

        return $data;
    }

}
