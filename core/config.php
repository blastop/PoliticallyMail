<?php
if($_SERVER["HTTPS"] != "on") {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}
//error_reporting(E_ALL); //
//ini_set("display_errors", 1); //
header("Content-Type: text/html; charset=utf-8");
date_default_timezone_set('UTC');

$basePath = __DIR__;
require_once($basePath.'/generic.php');
require_once($basePath.'/../DB/sqlPool.php');
require_once($basePath.'/constants.php');

DB::init('PoliticallyMail', 'db_login', 'db_pass');