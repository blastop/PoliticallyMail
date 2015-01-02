<?php
	if (isset($_SERVER['SERVER_NAME'])) unset($_SERVER['SERVER_NAME']);
    $_SERVER["HTTPS"] = "on";
	require_once(__DIR__."/../core/config.php");
	sqlPool::removeNotAccepted();
	require_once(__DIR__."/../ajax/load_dictionary.ajx");