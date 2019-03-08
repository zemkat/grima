<?php

require_once("../grima-lib.php");

class AdminAddUser extends GrimaTask {

	function do_task() {
		$user = GrimaUser::GetCurrentUser();
		if ($user['isAdmin']) {
			$username = $this['username'];
			$institution = $this['institution'] or $user['institution'];
			$newuser = new GrimaUser();
			$newuser['username'] = $username;
			$newuser['password'] = $this['password'];
			$newuser['institution'] = $institution;
			$newuser['isAdmin'] = false;
			$newuser->addToDB();
			$this->addMessage('success',"User $username at $institution successfully added.");
		} else {
			throw new Exception("User {$user['username']} (you) is not admin.");
		}
	}
}

AdminAddUser::RunIt();
