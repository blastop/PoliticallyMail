<?php

define('INDEX_START', (int)0);
define('MIN', (int)1);
define('MAX', 99999999);
/* Create a constant telling whether the script is running in 'cli' or 'web' */
define("CLI", php_sapi_name() === 'cli');
define("IS_DEV",
    CLI
    || strpos($_SERVER["SCRIPT_NAME"], "c:\\") !== false
    || (
        array_key_exists("HTTP_HOST",$_SERVER)
        && (
            strpos($_SERVER["HTTP_HOST"], "localhost") !== false
            || strpos($_SERVER["HTTP_HOST"], "127.0.0.1") !== false
        )
    ));

class generic {
    /**
     * @param $name
     * @param $default
     * @return integer
     */
    public static function getInputNumeric($name, $default=-1) {
        return isset($_POST[$name]) && $_POST[$name] > INDEX_START && doubleval($_POST[$name]) == $_POST[$name]
            ? doubleval($_POST[$name])
            : (
                isset($_GET[$name]) && $_GET[$name] > INDEX_START && doubleval($_GET[$name]) == $_GET[$name]
                    ? doubleval($_GET[$name])
                    : $default
            );
    }

    /**
     * @param $name
     * @param $default
     * @return string
     */
    public static function getInputString($name, $default=null) {
        return isset($_POST[$name]) && !empty($_POST[$name])
            ? $_POST[$name]
            : (
                isset($_GET[$name]) && !empty($_GET[$name])
                    ? $_GET[$name]
                    : $default
            );
    }
}