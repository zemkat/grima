<?php

require_once("../grima-lib.php");

class Login extends GrimaTask {

	function do_task() {
		global $grima;
		$user = GrimaUser::SetCurrentUser( 
			isset($this['username']) ? $this['username'] : '',
			isset($this['password']) ? $this['password'] : '',
			isset($this['institution']) ? $this['institution'] : ''
		);
		if ($user === false) {
			throw new Exception('Invalid login / password');
		}
	}

	function check_login() {
		return true;
	}

	function print_success() {
		if (isset($this['redirect_url']) and ($this['redirect_url'] != "")) {
			do_redirect($this['redirect_url']);
			exit;
		} else {
			$this->addMessage('success', "You are successfully logged in and can run grimas!");
			parent::print_success();
		}
	}
}

if (GrimaDb::isEmpty()) {
	require_once "../AdminAddInstitution/AdminAddInstitution.php";
} else {
	Login::RunIt();
}
