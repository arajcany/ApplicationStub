<?php
/**
 * Functions placed in this file MUST NOT break the MVC patterns defined by CakePHP
 */


/**
 *
 * @param array $list
 * @param int $p
 * @return array
 * @link http://www.php.net/manual/en/function.array-chunk.php#75022
 */
function partition(array $list, $p)
{
    $listlen = count($list);
    $partlen = floor($listlen / $p);
    $partrem = $listlen % $p;
    $partition = array();
    $mark = 0;
    for ($px = 0; $px < $p; $px++) {
        $incr = ($px < $partrem) ? $partlen + 1 : $partlen;
        $partition[$px] = array_slice($list, $mark, $incr);
        $mark += $incr;
    }
    return $partition;
}


/**
 * Check if an array is numerically indexed at the first level
 *
 * @param array $arr
 * @return bool
 */
function isSeqArr(array $arr)
{
    //empty array
    if ([] === $arr) {
        return false;
    }

    //check keys
    if (array_keys($arr) === range(0, count($arr) - 1)) {
        $return = true;
    } else {
        $return = false;
    }

    return $return;
}


/**
 * Convert value to boolean.
 *
 * @param $val
 * @return bool
 */
function asBool($val)
{
    if (is_string($val)) {
        $val = strtolower($val);
    }

    $true = [true, 'true', 1, '1', 't', 'yes', 'on'];
    $false = [false, 'false', 0, '0', 'f', 'no', 'off', null];

    if (in_array($val, $true, true)) {
        return true;
    }

    if (in_array($val, $false, true)) {
        return false;
    }

    return boolval($val);
}


/**
 * Convert value to a string.
 *
 * @param $val
 * @return bool
 */
function asString($val)
{
    if (is_string($val)) {
        return $val;
    }

    if ($val === null) {
        return 'null';
    }

    if ($val === true) {
        return 'true';
    }

    if ($val === false) {
        return 'false';
    }

    return false;
}


/**
 * Check if running in CLI mode. Slightly more reliable than the CakePHP defined constant
 *
 * @return bool
 */
function is_cli()
{
    if (defined('STDIN')) {
        return true;
    }

    if (php_sapi_name() === 'cli') {
        return true;
    }

    if (array_key_exists('SHELL', $_ENV)) {
        return true;
    }

    if (empty($_SERVER['REMOTE_ADDR']) and !isset($_SERVER['HTTP_USER_AGENT']) and count($_SERVER['argv']) > 0) {
        return true;
    }

    if (!array_key_exists('REQUEST_METHOD', $_SERVER)) {
        return true;
    }

    return false;
}
