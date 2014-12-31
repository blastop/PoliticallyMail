<?php
require_once(__DIR__.'/core/config.php');
//die ($action);
switch($action) {
    case -1:
        $show = true;
	case 1:
	case 2:
		if (generic::getInputString('admin') != 'some_long_pass_that_is got_by_GET') {
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: https://".$_SERVER["HTTP_HOST"]);
			exit();
		}
	case 3:
		require_once(__DIR__.'/ajax/'.$pages[$action].'.ajx');
		if (!$show) {
			/** $ret is being set in the *.ajx files */
            die(json_encode($ret));
        }
	default:
		die(null);
}