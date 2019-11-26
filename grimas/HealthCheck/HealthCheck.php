<?php

require_once("../grima-lib.php");

class HealthCheck extends GrimaTask {

	function check_login() {
		return true;
	}

	function do_task() {
	}
	function print_success() {
		print("<pre>");
		var_dump( array(
			'_SESSION'=>isset($_SESSION)?$_SESSION:null,
			'_ENV'=>$_ENV,
			'_SERVER'=>$_SERVER,
			'session_module_name()'=>session_module_name(),
			'session_save_path()'=>session_save_path(),
			'DATABASE_URL'=>getenv("DATABASE_URL"),
		) );
	}

}

HealthCheck::RunIt();
