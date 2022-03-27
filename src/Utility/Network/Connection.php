<?php

namespace App\Utility\Network;

use Exception;


class Connection
{
    /**
     * Check if there is Internet connection to the requested host:port
     * NOTE: If you need to check a fully qualified URL use checkUrlConnection() instead
     *
     * @param null $host
     * @param null $port
     * @param int $timeout
     * @return bool
     */
    public static function checkInternetConnection($host = null, $port = null, $timeout = 2)
    {
        //see if the network address:port is responding
        try {
            $fsock = @fsockopen($host, $port, $errno, $errstr, $timeout);
        } catch (Exception $e) {
            return false;
        }

        if (!$fsock) {
            return false;
        }

        fclose($fsock);
        return true;
    }


    /**
     * Check if there is Internet connection to the requested SMTP server
     *
     * @param null $host
     * @param null $port
     * @param int $timeout
     * @return bool
     */
    public static function checkSmtpConnection($host = null, $port = null, $timeout = 2)
    {
        //see if the network address:port is responding
        try {
            $fsock = @fsockopen($host, $port, $errno, $errstr, $timeout);
        } catch (Exception $e) {
            return false;
        }

        if (!$fsock) {
            return false;
        }

        $response = fread($fsock, 3);
        if ($response != '220') {
            return false;
        }

        fclose($fsock);
        return true;
    }


    /**
     * Check if you can write to and delete from a UNC path
     *
     * @param null $uncPathWithTrailingSlash
     * @return bool
     */
    public static function checkUncConnection($uncPathWithTrailingSlash)
    {
        $rndFile = $uncPathWithTrailingSlash . mt_rand() . ".txt";
        $contentToWrite = 'test';
        $contentLength = strlen($contentToWrite);

        $writeResult = @file_put_contents($rndFile, $contentToWrite);
        $deleteResult = @unlink($rndFile);

        if (($writeResult == $contentLength) && $deleteResult) {
            return true;
        } else {
            return false;
        }

    }


    /**
     * Check if there is an Internet connection to http/https server
     * @param $url
     * @param int $timeout
     * @return bool
     */
    public static function checkUrlConnection($url, $timeout = 2)
    {
        if (!$url_info = parse_url($url)) {
            return false;
        }

        switch ($url_info['scheme']) {
            case 'https':
                $scheme = 'ssl://';
                $port = 443;
                break;
            case 'http':
            default:
                $scheme = '';
                $port = 80;
        }

        //try via fsockopen
        try {
            $fsockopenResult = @fsockopen($scheme . $url_info['host'], $port, $errno, $errstr, $timeout);
            if ($fsockopenResult) {
                fclose($fsockopenResult);
            }
        } catch (Exception $e) {
            $fsockopenResult = false;
        }

        //try via stream_socket_client
        try {
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'allow_self_signed' => true
                ]
            ]);
            $urlAsTcp = "tcp://{$url_info['host']}:{$port}";
            $streamResult = @stream_socket_client($urlAsTcp, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $context);
        } catch (Exception $e) {
            $streamResult = false;
        }

        if ($fsockopenResult || $streamResult) {
            return true;
        }

        return false;
    }


}
