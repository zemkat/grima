<?php

require_once("../grima-lib.php");

class HealthCheck extends GrimaTask {

	function check_login() {
		return true;
	}

	function do_task() {
	}

	function print_success() {
		global $grima;
		print("<pre>");
		var_dump( array(
			'apikey'		=> isset($grima->apikey)?substr($grima->apikey,0,6) . "...":null,
			'server'		=> isset($grima->server)?$grima->server:null,
			'DATABASE_URL'		=> preg_replace("/(pass[a-z]*)=[^;]*/","$1=XXX",getenv("DATABASE_URL")),
			'SESSION_MODULE'	=> getenv("SESSION_MODULE"),
			'SESSION_NAME'		=> getenv("SESSION_NAME"),
			'SESSION_PATH'		=> getenv("SESSION_PATH"),
			'session_module_name()'	=> session_module_name(),
			'session_name()'	=> session_name(),
			'session_save_path()'	=> session_save_path(),
			'_COOKIE'		=> isset($_COOKIE )?$_COOKIE	:null,
			'_SESSION'		=> isset($_SESSION)?$_SESSION	:null,
			'_REQUEST'		=> isset($_REQUEST)?$_REQUEST	:null,
			'_ENV'			=> isset($_ENV    )?$_ENV	:null,
			'_SERVER'		=> isset($_SERVER )?$_SERVER	:null,
		) );
	}

}

HealthCheck::RunIt();
