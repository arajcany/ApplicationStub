<?php

namespace App\Utility\Feedback;

use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\ORM\Query;
use DebugKit\DebugSql;
use SqlFormatter;

class DebugSqlCapture
{

    /**
     * Debug an SQL Query
     *
     * @param Query $query
     * @param bool $showHtml
     * @return array|false|mixed|string
     */
    public static function captureDump(Query $query, $showHtml = true)
    {
        $showValues = true;
        $stackDepth = 0;

        $originalDebugValue = Configure::read('debug');
        Configure::write('debug', true);

        ob_start();
        DebugSql::sql($query, $showValues, false, $stackDepth);
        $data = ob_get_contents();
        ob_end_clean();

        $data = str_replace('########## DEBUG ##########', '', $data);
        $data = str_replace('###########################', '', $data);
        $data = explode("\n", $data);
        $data = $data[2];

        if ($showHtml) {
            $data = SqlFormatter::format($data);
        }

        Configure::write('debug', $originalDebugValue);

        return $data;
    }


}