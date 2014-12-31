<?php

define('FOLDER','PoliticallyMail');
define('WEB_DNS','127.0.0.1'); /** Should be a real IP from where the dictionary is taken */
define('TITLE','Politically Mail');
define('EMAIL','webmaster@veryzol.co.il');

//print_r($_SERVER);
if (IS_DEV) {
	define('DNS', 'localhost/'.FOLDER);
	ini_set('display_errors',1);
    error_reporting(E_ALL);
} else {
    define('DNS', $_SERVER['HTTP_HOST'].'/'.FOLDER);
}

$path = 'https://'.(in_array('HTTP_HOST', $_SERVER) ? $_SERVER['HTTP_HOST'] : DNS).'/'.FOLDER.'/';
$ret = array('return' => false);

if (empty($_GET) && !IS_DEV && ($_SERVER["REQUEST_METHOD"] != "POST" || !empty($_POST) || !($_POST = json_decode(file_get_contents("php://input"), true)))) {
	die(json_encode($ret));
}

$pages = array(
    -1 => 'show_dictionary',
    1 => 'load_dictionary',
    2 => 'update',
	3 => 'suggest'
);

/**
	a - action
	s - status
	w - word
	wid - wordID
    show - show/reload dictionary
*/

$action = generic::getInputNumeric('a');
$status = generic::getInputNumeric('s');
$word = generic::getInputString('w');
$email = generic::getInputString('e');
$wordID = generic::getInputNumeric('wid');

if (!empty($word)) {
	$whitespace_chars = '((\s\s)|[\s]|[^a-zא-תа-я])+';
	$word = trim(preg_replace('/'.$whitespace_chars.'/', ' ', strtolower($word)));
}

$show = false;