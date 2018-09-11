<?php

require_once("../grima-lib.php");

class AdminAdminUser extends GrimaTask {

	function do_task() {
		$user = GrimaUser::GetCurrentUser();
		if ($user['isAdmin']) {
			$newuser = GrimaUser::LookupUser($this['username'],$user['institution']);
			if ($newuser === false) {
				throw new Exception( "User '{$this['username']}' does not exist.");
			}
			if ($newuser['isAdmin']) {
				$this->addMessage('warning',
					"User {$newuser['username']} is already admin.");
				return;
			}
			$newuser['isAdmin'] = true; 
			$newuser->updateDB();
			$this->addMessage('success',"User {$newuser['username']} is now admin.");
		} else {
			throw new Exception("User {$user['username']} is not admin.");
		}
	}
}

AdminAdminUser::RunIt();
