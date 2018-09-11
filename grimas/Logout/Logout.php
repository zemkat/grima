<?php

require_once("../grima-lib.php");

class Logout extends GrimaTask {

	function do_task() {
		global $grima;
		$grima->session_destroy();
		$this->addMessage('success',"You have been logged out.");
	}

	function check_login() {
		return true;
	}
}

Logout::RunIt();
