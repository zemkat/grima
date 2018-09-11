<?php

require_once("../grima-lib.php");

class AdminDemoteUser extends GrimaTask {

	function do_task() {
		$user = GrimaUser::GetCurrentUser();
		if ($user['isAdmin']) {
			$newuser = GrimaUser::LookupUser($this['username'],$user['institution']);
			if (!$newuser->isAdmin) {
				$this->addMessage('warning',"User {$newuser['username']} is not admin.");
				return;
			}
			$newuser['isAdmin'] = false; 
			$newuser->updateDB();
			$this->addMessage('success',"User {$newuser['username']} is no longer admin.");
		} else {
			throw new Exception('error',"User {$user['username']} is not admin.");
		}
	}
}

AdminDemoteUser::RunIt();
