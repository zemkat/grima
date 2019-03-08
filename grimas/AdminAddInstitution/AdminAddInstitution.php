<?php

require_once("../grima-lib.php");

$firstRun = GrimaDb::isEmpty();

class AdminAddInstitution extends GrimaTask {

	function setup_splat() {
		parent::setup_splat();
		global $firstRun;
		if ($firstRun) {
			if (!isset($this['username'])) {
				$this->AddMessage('success',"Welcome to Grima! You'll need to configure an API-key before grima has enough power to whisper into Alma's ear.");
			} else if (isset($this['redirect_url'])) {
				$this->splatVars['redirect_url'] = $this['redirect_url'];
			} else {
				$this->splatVars['redirect_url'] = "../PrintBib/PrintBib.php";
			}
		}
	}

	function do_task() {
		global $firstRun;
		if ( !$firstRun ) {
			$user = GrimaUser::GetCurrentUser();
			if (!$user['isAdmin']) {
				throw new Exception("User {$user['username']} is not admin.");
			}
		}
		$inst = new GrimaInstitution();
		$inst['institution'] = $this['institution'];
		$inst['server'] = $this['server'];
		$inst['apikey'] = $this['apikey'];
		try {
			$inst->addToDB();
			$this->addMessage('success',"Institution '{$inst['institution']}' successfully added.");
		} catch(Exception $e) {
			$this->addMessage('danger',"Failed to add institution {$inst['institution']}");
			return;
		}
		$newAdmin = new GrimaUser();
		$newAdmin['username'] = $this['username'];
		$newAdmin['password'] = $this['password'];
		$newAdmin['institution'] = $this['institution'];
		$newAdmin['isAdmin'] = $firstRun ? 2 : 1;
		$newAdmin->addToDB();
		$this->addMessage('success',"User '{$newAdmin['username']}' successfully added as admin of '{$inst['institution']}'.");
		GrimaUser::SetCurrentUser($newAdmin['username'],$newAdmin['password'],$newAdmin['institution']);
		$this->addMessage('success',"You are now logged in as '{$newAdmin['username']}'");
	}

	function check_login() {
		global $firstRun;
		if ( $firstRun ) {
			return true;
		} else {
			return parent::check_login();
		}
	}
}

AdminAddInstitution::RunIt();
