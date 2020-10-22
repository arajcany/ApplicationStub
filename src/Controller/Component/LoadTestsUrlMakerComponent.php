<?php

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;

/**
 * LoadTestsUrlMaker component
 */
class LoadTestsUrlMakerComponent extends Component
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];


    /**
     * @param array $config
     */
    public function initialize(array $config)
    {

    }

    public function insertVariables(string $url)
    {
        $newUrl = $url;

        //replace random integers
        $var = 'rnd_int';
        $defaultMin = 0;
        $defaultMax = PHP_INT_MAX;
        $matches = $this->findMatches($url, "{" . $var, "}");
        foreach ($matches[0] as $k => $toReplace) {
            if ($params = $matches[1][$k]) {
                $params = trim($params, ":");
                $re = '/\d+/';
                preg_match_all($re, $params, $numbers);
                if (!isset($numbers[0])) {
                    $min = $defaultMin;
                    $max = $defaultMax;
                } elseif (count($numbers[0]) == 1) {
                    $min = $numbers[0][0];
                    $max = $defaultMax;
                } elseif (count($numbers[0]) >= 1) {
                    $min = $numbers[0][0];
                    $max = $numbers[0][1];
                } else {
                    $min = $defaultMin;
                    $max = $defaultMax;
                }
                $randomNumber = mt_rand($min, $max);
            } else {
                $randomNumber = mt_rand();
            }
            $newUrl = str_replace($toReplace, $randomNumber, $newUrl);
        }

        //replace random words
        $var = 'rnd_word';
        $defaultMin = 1;
        $defaultMax = 5;
        $matches = $this->findMatches($url, "{" . $var, "}");
        foreach ($matches[0] as $k => $toReplace) {
            if ($params = $matches[1][$k]) {
                $params = trim($params, ":");
                $re = '/\d+/';
                preg_match_all($re, $params, $numbers);
                if (!isset($numbers[0])) {
                    $min = $defaultMin;
                    $max = $defaultMax;
                } elseif (count($numbers[0]) == 1) {
                    $min = $numbers[0][0];
                    $max = $numbers[0][0];
                } elseif (count($numbers[0]) >= 1) {
                    $min = $numbers[0][0];
                    $max = $numbers[0][1];
                } else {
                    $min = $defaultMin;
                    $max = $defaultMax;
                }
                $randomWords = [];
                $howManyWords = mt_rand($min, $max);
                foreach (range(1, $howManyWords) as $currentNumber) {
                    $randomWords[] = $this->readable_random_string();
                }
                $randomWords = implode("-", $randomWords);
            } else {
                $randomWords = $this->readable_random_string();
            }
            $newUrl = str_replace($toReplace, $randomWords, $newUrl);
        }

        return $newUrl;
    }

    public function findMatches($input, $startTag, $endTag)
    {
        $delimiter = '#';
        $regex = $delimiter . preg_quote($startTag, $delimiter)
            . '(.*?)'
            . preg_quote($endTag, $delimiter)
            . $delimiter
            . 's';
        preg_match_all($regex, $input, $matches);

        return $matches;
    }

    public function readable_random_string($length = null)
    {
        if ($length == null) {
            $length = mt_rand(3, 9);
        }

        $string = '';
        $vowels = array("a", "e", "i", "o", "u");
        $consonants = array(
            'b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm',
            'n', 'p', 'r', 's', 't', 'v', 'w', 'x', 'y', 'z'
        );

        $max = $length / 2;
        for ($i = 1; $i <= $max; $i++) {
            $string .= $consonants[rand(0, 19)];
            $string .= $vowels[rand(0, 4)];
        }

        return $string;
    }


}
