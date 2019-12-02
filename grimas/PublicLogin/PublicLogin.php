<?php

require_once("../grima-lib.php");

class PublicLogin extends GrimaTask {

	function setup_splat() {
		parent::setup_splat();
		if (GrimaDb::isStateless()) {
			if (!(isset($this['apikey']) && $this['apikey'])) {
				$this->AddMessage('success',"Welcome to Grima! You'll need to configure an API-key before grima has enough power to whisper into Alma's ear.");
			}
		}
	}

	function do_task() {
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

PublicLogin::RunIt();
